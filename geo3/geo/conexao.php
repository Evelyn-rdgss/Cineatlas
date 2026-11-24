<?php
declare(strict_types=1);

/* ===== Configuração do banco (XAMPP padrão) ===== */
const DB_HOST = 'sql302.infinityfree.com';
const DB_NAME = 'if0_43517900_cineatlas';
const DB_USER = 'if0_43517900';
const DB_PASS = 'Cineatlas261126';      // no XAMPP o root vem sem senha; em produção use um usuário próprio

/* ===== Administrador "embutido" no código =====
   Funciona em qualquer computador, mesmo sem a tabela `administradores` no banco.
   Aqui fica o HASH da senha (não a senha em si). Para gerar o hash, rode no terminal:
   php -r "echo password_hash('SUA_SENHA', PASSWORD_DEFAULT);"
   (no XAMPP/Windows: C:\xampp\php\php.exe -r "echo password_hash('SUA_SENHA', PASSWORD_DEFAULT);")
   e cole o resultado abaixo, entre aspas simples. */
const ADMIN_EMAIL      = 'equipe@cineatlas.edu.br';
const ADMIN_SENHA_HASH = '$2y$10$ysNDyCgaNzZau0Iwk6f6fOhei8V6NVrvedfYgP0x9vGj3j8Hcu3Ui';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Não foi possível conectar ao banco de dados. Verifique o MySQL e o arquivo conexao.php.');
        }
    }
    return $pdo;
}