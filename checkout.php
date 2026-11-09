<?php
require_once __DIR__ . '/includes/helpers.php';
require_login();

$items = cart_items();
if (!$items) { flash('warning', 'Seu carrinho está vazio.'); redirect('/carrinho.php'); }
$total = cart_total($items);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $metodo = $_POST['pagamento'] ?? '';
    if (!in_array($metodo, ['PIX', 'Boleto', 'Cartao'], true)) {
        flash('danger', 'Selecione um método de pagamento.');
        redirect('/checkout.php');
    }

    $pdo = db();
    try {
        $pdo->beginTransaction();
        $pedidoTotal = 0;
        $linhas = [];
        // Trava as linhas de produto e revalida estoque/preço atuais
        $sel = $pdo->prepare("SELECT id, nome, preco, estoque FROM produtos WHERE id = ? AND ativo = 1 FOR UPDATE");
        foreach (cart() as $pid => $qtd) {
            $sel->execute([$pid]);
            $p = $sel->fetch();
            if (!$p || $p['estoque'] < $qtd) throw new RuntimeException('Estoque insuficiente para: ' . ($p['nome'] ?? "produto #$pid"));
            $pedidoTotal += $p['preco'] * $qtd;
            $linhas[] = [$pid, $qtd, $p['preco']];
        }

        $pdo->prepare("INSERT INTO pedidos (usuario_id, forma_pagamento, total, status) VALUES (?, ?, ?, 'Pago')")
            ->execute([current_user()['id'], $metodo, $pedidoTotal]);
        $pedidoId = (int)$pdo->lastInsertId();

        $insItem = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        $baixa   = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
        foreach ($linhas as [$pid, $qtd, $preco]) {
            $insItem->execute([$pedidoId, $pid, $qtd, $preco]);
            $baixa->execute([$qtd, $pid]);
        }
        $pdo->commit();
        unset($_SESSION['cart']);
        redirect('/pedido_sucesso.php?id=' . $pedidoId);
    } catch (RuntimeException $ex) {
        $pdo->rollBack();
        flash('danger', $ex->getMessage());
        redirect('/carrinho.php');
    }
}

$title = 'Checkout';
include __DIR__ . '/includes/header.php';
?>
<h2 class="mb-4">Finalizar compra</h2>
<div class="row g-4">
  <div class="col-md-7">
    <ul class="list-group">
      <?php foreach ($items as $i): ?>
        <li class="list-group-item d-flex justify-content-between"><span><?= e($i['nome']) ?> × <?= $i['qtd'] ?></span><span><?= money($i['subtotal']) ?></span></li>
      <?php endforeach; ?>
      <li class="list-group-item d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($total) ?></span></li>
    </ul>
  </div>
  <div class="col-md-5">
    <form method="post" class="card card-body">
      <?= csrf_field() ?>
      <h5>Pagamento (simulado)</h5>
      <?php foreach (['PIX' => 'PIX', 'Boleto' => 'Boleto', 'Cartao' => 'Cartão'] as $v => $l): ?>
        <div class="form-check my-1"><input class="form-check-input" type="radio" name="pagamento" id="p<?= $v ?>" value="<?= $v ?>" required>
        <label class="form-check-label" for="p<?= $v ?>"><?= $l ?></label></div>
      <?php endforeach; ?>
      <button class="btn btn-success mt-3">Confirmar pagamento</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
