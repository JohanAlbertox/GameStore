<?php
require_once __DIR__ . '/../includes/helpers.php';
require_admin();
$title = 'Painel Admin';
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-4">Painel Administrativo</h2>
<div class="row g-3">
  <div class="col-md-6"><a class="btn btn-primary w-100 py-4" href="/admin/produtos.php">📦 Gerenciar produtos</a></div>
  <div class="col-md-6"><a class="btn btn-secondary w-100 py-4" href="/admin/pedidos.php">🧾 Ver pedidos</a></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
