<?php
require_once __DIR__ . '/includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {
        flash('danger', 'Preencha nome, e-mail válido e senha com no mínimo 6 caracteres.');
    } else {
        $st = db()->prepare("SELECT id FROM usuarios WHERE email = ?");
        $st->execute([$email]);
        if ($st->fetch()) {
            flash('danger', 'Este e-mail já está cadastrado.');
        } else {
            $ins = db()->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, 'cliente')");
            $ins->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);
            flash('success', 'Cadastro realizado! Faça login.');
            redirect('/login.php');
        }
    }
    redirect('/cadastro.php');
}
$title = 'Cadastro';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-5">
  <h2 class="mb-3">Criar conta</h2>
  <form method="post" class="card card-body">
    <?= csrf_field() ?>
    <label class="form-label">Nome</label><input name="nome" class="form-control mb-3" required>
    <label class="form-label">E-mail</label><input type="email" name="email" class="form-control mb-3" required>
    <label class="form-label">Senha (mín. 6)</label><input type="password" name="senha" class="form-control mb-3" minlength="6" required>
    <button class="btn btn-primary">Cadastrar</button>
  </form>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
