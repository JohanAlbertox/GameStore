<?php
require_once __DIR__ . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare("SELECT * FROM usuarios WHERE email = ?");
    $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['senha'] ?? '', $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'nome' => $u['nome'], 'perfil' => $u['perfil']];
        $dest = $_SESSION['redirect_after_login'] ?? ($u['perfil'] === 'admin' ? '/admin/index.php' : '/index.php');
        unset($_SESSION['redirect_after_login']);
        redirect($dest);
    }
    flash('danger', 'E-mail ou senha inválidos.');
    redirect('/login.php');
}
$title = 'Entrar';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5">
  <h2 class="mb-3">Entrar</h2>
  <form method="post" class="card card-body">
    <?= csrf_field() ?>
    <label class="form-label">E-mail</label><input type="email" name="email" class="form-control mb-3" required>
    <label class="form-label">Senha</label><input type="password" name="senha" class="form-control mb-3" required>
    <button class="btn btn-primary">Entrar</button>
    <small class="mt-3">Não tem conta? <a href="/cadastro.php">Cadastre-se</a></small>
  </form>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
