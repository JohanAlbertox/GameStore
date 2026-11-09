<?php
require_once __DIR__ . '/../includes/helpers.php';
require_admin();

$pedidos = db()->query("SELECT p.*, u.nome AS cliente, u.email FROM pedidos p JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.id DESC")->fetchAll();
$stItens = db()->prepare("SELECT i.*, pr.nome FROM itens_pedido i JOIN produtos pr ON pr.id = i.produto_id WHERE i.pedido_id = ?");
$title = 'Pedidos';
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Pedidos realizados</h2>
<?php if (!$pedidos): ?><div class="alert alert-info">Nenhum pedido ainda.</div><?php endif; ?>
<?php foreach ($pedidos as $p): $stItens->execute([$p['id']]); ?>
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between flex-wrap">
      <span><strong>Pedido #<?= $p['id'] ?></strong> — <?= e($p['cliente']) ?> (<?= e($p['email']) ?>)</span>
      <span><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?> • <?= e($p['forma_pagamento']) ?> • <span class="badge bg-success"><?= e($p['status']) ?></span></span>
    </div>
    <ul class="list-group list-group-flush">
      <?php foreach ($stItens as $i): ?>
        <li class="list-group-item d-flex justify-content-between"><span><?= e($i['nome']) ?> × <?= $i['quantidade'] ?> (<?= money($i['preco_unitario']) ?> un.)</span><span><?= money($i['quantidade'] * $i['preco_unitario']) ?></span></li>
      <?php endforeach; ?>
    </ul>
    <div class="card-footer text-end fw-bold">Total: <?= money($p['total']) ?></div>
  </div>
<?php endforeach; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
