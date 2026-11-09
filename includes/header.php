<?php require_once __DIR__ . '/helpers.php'; $u = current_user(); ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'GameStore') ?> • GameStore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/index.php">🎮 GameStore</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <?php if ($u && $u['perfil'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/admin/index.php">Painel Admin</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="/carrinho.php">🛒 Carrinho <span class="badge bg-primary"><?= cart_count() ?></span></a></li>
        <?php if ($u): ?>
          <li class="nav-item"><span class="navbar-text">Olá, <?= e(explode(' ', $u['nome'])[0]) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="/logout.php">Sair</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/login.php">Entrar</a></li>
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="/cadastro.php">Cadastrar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container my-4 flex-grow-1">
<?php foreach ($_SESSION['flash'] ?? [] as [$t, $m]): ?>
  <div class="alert alert-<?= e($t) ?> alert-dismissible fade show"><?= e($m) ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endforeach; unset($_SESSION['flash']); ?>
