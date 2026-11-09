<?php
require_once __DIR__ . '/includes/helpers.php';
require_login();
$st = db()->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$st->execute([(int)($_GET['id'] ?? 0), current_user()['id']]);
$pedido = $st->fetch();
if (!$pedido) redirect('/index.php');
$itens = db()->prepare("SELECT i.*, p.nome FROM itens_pedido i JOIN produtos p ON p.id = i.produto_id WHERE i.pedido_id = ?");
$itens->execute([$pedido['id']]);
$title = 'Pedido confirmado';
include __DIR__ . '/includes/header.php';
?>
<div class="text-center mb-4">
  <h2 class="text-success">✔ Pagamento confirmado!</h2>
  <p>Pedido <strong>#<?= $pedido['id'] ?></strong> • <?= e($pedido['forma_pagamento']) ?> • <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></p>
</div>
<ul class="list-group mb-3">
  <?php foreach ($itens as $i): ?>
    <li class="list-group-item d-flex justify-content-between"><span><?= e($i['nome']) ?> × <?= $i['quantidade'] ?></span><span><?= money($i['quantidade'] * $i['preco_unitario']) ?></span></li>
  <?php endforeach; ?>
  <li class="list-group-item d-flex justify-content-between fw-bold"><span>Total</span><span><?= money($pedido['total']) ?></span></li>
</ul>
<a href="/index.php" class="btn btn-primary">Voltar à loja</a>
<?php include __DIR__ . '/includes/footer.php'; ?>
