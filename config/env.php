<?php
// Carrega variáveis do arquivo .env para $_ENV (sem libs externas)
function loadEnv(string $path): void
{
    if (!is_file($path)) {
        die('Arquivo .env não encontrado. Copie .env.example para .env e configure.');
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v, " \t\"'");
    }
}
loadEnv(__DIR__ . '/../.env');
