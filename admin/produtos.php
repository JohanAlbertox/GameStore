<?php
require_once __DIR__ . '/../includes/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'excluir') {
        try {
            db()->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
            flash('success', 'Produto excluído.');
        } catch (PDOException $ex) {
            // Já existe em pedidos: apenas desativa para preservar o histórico
            db()->prepare("UPDATE produtos SET ativo = 0 WHERE id = ?")->execute([$id]);
            flash('warning', 'Produto possui vendas; foi desativado em vez de excluído.');
        }
    } elseif ($acao === 'toggle') {
        db()->prepare("UPDATE produtos SET ativo = 1 - ativo WHERE id = ?")->execute([$id]);
        flash('info', 'Status atualizado.');
    }
    redirect('/admin/produtos.php');
}

$produtos = db()->query("SELECT p.*, c.nome AS categoria FROM produtos p JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC")->fetchAll();
$title = 'Produtos';
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between mb-3">
  <h2>Produtos</h2>
  <a href="/admin/produto_form.php" class="btn btn-success">+ Novo produto</a>
</div>
<div class="table-responsive"><table class="table table-sm align-middle">
  <thead><tr><th>#</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($produtos as $p): ?>
    <tr class="<?= $p['ativo'] ? '' : 'table-secondary' ?>">
      <td><?= $p['id'] ?></td><td><?= e($p['nome']) ?></td><td><?= e($p['categoria']) ?></td>
      <td><?= money($p['preco']) ?></td><td><?= $p['estoque'] ?></td>
      <td><?= $p['ativo'] ? 'Ativo' : 'Inativo' ?></td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-primary" href="/admin/produto_form.php?id=<?= $p['id'] ?>">Editar</a>
        <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="acao" value="toggle">
          <button class="btn btn-sm btn-outline-secondary"><?= $p['ativo'] ? 'Desativar' : 'Ativar' ?></button></form>
        <form method="post" class="d-inline" data-confirm="Excluir este produto?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="acao" value="excluir">
          <button class="btn btn-sm btn-outline-danger">Excluir</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
