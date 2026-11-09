<?php
require_once __DIR__ . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $acao = $_POST['acao'] ?? '';
    $id   = (int)($_POST['id'] ?? 0);
    $qtd  = max(1, (int)($_POST['qtd'] ?? 1));

    $st = db()->prepare("SELECT estoque FROM produtos WHERE id = ? AND ativo = 1");
    $st->execute([$id]);
    $estoque = $st->fetchColumn();

    if ($acao === 'remover') {
        unset($_SESSION['cart'][$id]);
        flash('info', 'Item removido.');
    } elseif ($estoque === false) {
        flash('danger', 'Produto inválido.');
    } elseif ($acao === 'add') {
        $novo = ($_SESSION['cart'][$id] ?? 0) + $qtd;
        if ($novo > $estoque) { $novo = (int)$estoque; flash('warning', "Quantidade limitada ao estoque ($estoque)."); }
        else flash('success', 'Produto adicionado ao carrinho.');
        if ($novo > 0) $_SESSION['cart'][$id] = $novo;
    } elseif ($acao === 'atualizar') {
        if ($qtd > $estoque) { $qtd = (int)$estoque; flash('warning', "Estoque disponível: $estoque."); }
        $_SESSION['cart'][$id] = $qtd;
    }
    redirect('/carrinho.php');
}

$items = cart_items();
$total = cart_total($items);
$title = 'Carrinho';
include __DIR__ . '/includes/header.php';
?>
<h2 class="mb-4">Seu carrinho</h2>
<?php if (!$items): ?>
  <div class="alert alert-info">Carrinho vazio. <a href="/index.php">Continue comprando</a>.</div>
<?php else: ?>
<div class="table-responsive">
<table class="table align-middle">
  <thead><tr><th>Produto</th><th>Preço</th><th style="width:130px">Qtd</th><th>Subtotal</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($items as $i): ?>
    <tr>
      <td><a href="/produto.php?id=<?= $i['id'] ?>"><?= e($i['nome']) ?></a></td>
      <td><?= money($i['preco']) ?></td>
      <td>
        <form method="post">
          <?= csrf_field() ?><input type="hidden" name="acao" value="atualizar"><input type="hidden" name="id" value="<?= $i['id'] ?>">
          <input type="number" name="qtd" class="form-control form-control-sm qty-input" value="<?= $i['qtd'] ?>" min="1" max="<?= $i['estoque'] ?>">
        </form>
      </td>
      <td><?= money($i['subtotal']) ?></td>
      <td>
        <form method="post" data-confirm="Remover este item?">
          <?= csrf_field() ?><input type="hidden" name="acao" value="remover"><input type="hidden" name="id" value="<?= $i['id'] ?>">
          <button class="btn btn-sm btn-outline-danger">Remover</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="3" class="text-end">Total geral:</th><th class="fs-5"><?= money($total) ?></th><th></th></tr></tfoot>
</table>
</div>
<div class="d-flex justify-content-between">
  <a href="/index.php" class="btn btn-outline-secondary">Continuar comprando</a>
  <a href="/checkout.php" class="btn btn-success btn-lg">Finalizar compra</a>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
