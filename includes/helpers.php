<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($v): string { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
function redirect(string $url): void { header("Location: $url"); exit; }

// ---- Mensagens flash ----
function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }

// ---- CSRF ----
function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        die('Token CSRF inválido.');
    }
}

// ---- Autenticação / autorização (validação no servidor) ----
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void {
    if (!current_user()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        flash('warning', 'Faça login para continuar.');
        redirect('/login.php');
    }
}
function require_admin(): void {
    $u = current_user();
    if (!$u || $u['perfil'] !== 'admin') {
        http_response_code(403);
        flash('danger', 'Acesso restrito a administradores.');
        redirect('/login.php');
    }
}

// ---- Carrinho em $_SESSION: [produto_id => quantidade] ----
function cart(): array { return $_SESSION['cart'] ?? []; }
function cart_count(): int { return array_sum(cart()); }

// Retorna itens do carrinho com dados atuais do banco + subtotal
function cart_items(): array {
    $cart = cart();
    if (!$cart) return [];
    $ids = array_keys($cart);
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = db()->prepare("SELECT * FROM produtos WHERE id IN ($in) AND ativo = 1");
    $st->execute($ids);
    $items = [];
    foreach ($st->fetchAll() as $p) {
        $p['qtd'] = min($cart[$p['id']], $p['estoque']);
        $p['subtotal'] = $p['qtd'] * $p['preco'];
        $items[] = $p;
    }
    return $items;
}
function cart_total(array $items): float { return array_sum(array_column($items, 'subtotal')); }
