<?php
require_once __DIR__ . '/includes/helpers.php';
$st = db()->prepare("SELECT p.*, c.nome AS categoria FROM produtos p JOIN categorias c ON c.id = p.categoria_id WHERE p.id = ? AND p.ativo = 1");
$st->execute([(int)($_GET['id'] ?? 0)]);
$p = $st->fetch();
if (!$p) { http_response_code(404); flash('danger', 'Produto não encontrado.'); redirect('/index.php'); }
$title = $p['nome'];
include __DIR__ . '/includes/header.php';
?>
<div class="row g-4">
  <div class="col-md-6"><img src="<?= e($p['imagem']) ?>" class="detail-img rounded border" alt="<?= e($p['nome']) ?>"></div>
  <div class="col-md-6">
    <span class="badge bg-secondary"><?= e($p['categoria']) ?></span>
    <h2 class="mt-2"><?= e($p['nome']) ?></h2>
    <small class="text-muted">Código: #<?= $p['id'] ?></small>
    <p class="display-6 text-primary my-3"><?= money($p['preco']) ?></p>
    <p><?= nl2br(e($p['descricao'])) ?></p>
    <?php if ($p['estoque'] > 0): ?>
      <p class="text-success fw-semibold">✔ Em estoque (<?= $p['estoque'] ?> unidades)</p>
      <form method="post" action="/carrinho.php" class="d-flex gap-2">
        <?= csrf_field() ?>
        <input type="hidden" name="acao" value="add">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <input type="number" name="qtd" class="form-control w-25" value="1" min="1" max="<?= $p['estoque'] ?>">
        <button class="btn btn-primary">Adicionar ao carrinho</button>
      </form>
    <?php else: ?>
      <p class="text-danger fw-semibold">✖ Produto indisponível</p>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
