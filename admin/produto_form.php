<?php
require_once __DIR__ . '/../includes/helpers.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$p = ['nome' => '', 'descricao' => '', 'preco' => '', 'estoque' => 0, 'imagem' => '', 'categoria_id' => 0, 'ativo' => 1];
if ($id) {
    $st = db()->prepare("SELECT * FROM produtos WHERE id = ?");
    $st->execute([$id]);
    $p = $st->fetch() ?: redirect('/admin/produtos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d = [
        trim($_POST['nome'] ?? ''), trim($_POST['descricao'] ?? ''),
        (float)str_replace(',', '.', $_POST['preco'] ?? '0'), max(0, (int)($_POST['estoque'] ?? 0)),
        trim($_POST['imagem'] ?? ''), (int)($_POST['categoria_id'] ?? 0),
    ];
    if ($d[0] === '' || $d[2] < 0 || !$d[5]) {
        flash('danger', 'Informe nome, preço válido e categoria.');
        redirect($_SERVER['REQUEST_URI']);
    }
    if ($id) {
        db()->prepare("UPDATE produtos SET nome=?, descricao=?, preco=?, estoque=?, imagem=?, categoria_id=? WHERE id=?")->execute([...$d, $id]);
        flash('success', 'Produto atualizado.');
    } else {
        db()->prepare("INSERT INTO produtos (nome, descricao, preco, estoque, imagem, categoria_id) VALUES (?,?,?,?,?,?)")->execute($d);
        flash('success', 'Produto cadastrado.');
    }
    redirect('/admin/produtos.php');
}

$cats = db()->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
$title = $id ? 'Editar produto' : 'Novo produto';
include __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3"><?= e($title) ?></h2>
<form method="post" class="card card-body">
  <?= csrf_field() ?>
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label">Nome</label><input name="nome" class="form-control" value="<?= e($p['nome']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Categoria</label>
      <select name="categoria_id" class="form-select" required>
        <option value="">Selecione...</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $p['categoria_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-12"><label class="form-label">Descrição</label><textarea name="descricao" class="form-control" rows="3"><?= e($p['descricao']) ?></textarea></div>
    <div class="col-md-4"><label class="form-label">Preço (R$)</label><input name="preco" class="form-control" value="<?= e($p['preco']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">Estoque</label><input type="number" min="0" name="estoque" class="form-control" value="<?= e($p['estoque']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">URL da imagem</label><input name="imagem" class="form-control" value="<?= e($p['imagem']) ?>"></div>
  </div>
  <div class="mt-3"><button class="btn btn-primary">Salvar</button> <a href="/admin/produtos.php" class="btn btn-outline-secondary">Cancelar</a></div>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>
