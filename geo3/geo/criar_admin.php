<?php
declare(strict_types=1);

/*
 * Cria (ou atualiza a senha de) um administrador na tabela `administradores`.
 * Uso único, SOMENTE pelo terminal — não funciona se aberto no navegador.
 *
 *   php criar_admin.php "equipe@cineatlas.edu.br" "SuaSenhaForte" "Equipe CineAtlas"
 *
 * Coloque este arquivo na mesma pasta do conexao.php. Depois de usar, pode apagá-lo.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Execute este script pelo terminal.');
}

require_once __DIR__ . '/conexao.php';

$email = mb_strtolower(trim($argv[1] ?? ''));
$senha = $argv[2] ?? '';
$nome  = trim($argv[3] ?? 'Equipe CineAtlas');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($senha) < 8) {
    exit("Uso: php criar_admin.php \"email@dominio\" \"senha-com-8+-caracteres\" [\"Nome\"]\n");
}

$st = db()->prepare(
    'INSERT INTO administradores (nome, email, senha_hash, ativo) VALUES (?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE senha_hash = VALUES(senha_hash), nome = VALUES(nome), ativo = 1'
);
$st->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT)]);

echo "Administrador pronto: $email\n";
