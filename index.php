<?php
require_once __DIR__ . '/includes/helpers.php';
$title = 'Vitrine';

$busca = trim($_GET['q'] ?? '');
$cat   = (int)($_GET['cat'] ?? 0);

$sql = "SELECT p.*, c.nome AS categoria FROM produtos p JOIN categorias c ON c.id = p.categoria_id WHERE p.ativo = 1";
$params = [];
if ($busca !== '') { $sql .= " AND (p.nome LIKE ? OR p.descricao LIKE ?)"; $params[] = "%$busca%"; $params[] = "%$busca%"; }
if ($cat > 0)      { $sql .= " AND p.categoria_id = ?"; $params[] = $cat; }
$sql .= " ORDER BY p.nome";

$st = db()->prepare($sql);
$st->execute($params);
$produtos   = $st->fetchAll();
$categorias = db()->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<form class="row g-2 mb-4" method="get">
  <div class="col-md-6"><input class="form-control" name="q" placeholder="Buscar produtos..." value="<?= e($busca) ?>"></div>
  <div class="col-md-4">
    <select class="form-select" name="cat">
      <option value="0">Todas as categorias</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2 d-grid"><button class="btn btn-primary">Filtrar</button></div>
</form>

<?php if (!$produtos): ?>
  <div class="alert alert-info">Nenhum produto encontrado.</div>
<?php endif; ?>
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
<?php foreach ($produtos as $p): ?>
  <div class="col">
    <div class="card h-100 card-product">
      <img src="<?= e($p['imagem']) ?>" class="card-img-top product-img" alt="<?= e($p['nome']) ?>">
      <div class="card-body d-flex flex-column">
        <span class="badge bg-secondary align-self-start mb-2"><?= e($p['categoria']) ?></span>
        <h6 class="card-title"><?= e($p['nome']) ?></h6>
        <p class="fs-5 fw-bold text-primary mt-auto mb-2"><?= money($p['preco']) ?></p>
        <?php if ($p['estoque'] > 0): ?>
          <a href="/produto.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">Ver detalhes</a>
        <?php else: ?>
          <a href="/produto.php?id=<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm">Esgotado</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
