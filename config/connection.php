<?php
// Configurações de sessão (apenas se sessão não estiver ativa)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'enterprise');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Erro de conexão com o banco de dados: ' . $e->getMessage());

    die('Erro ao conectar ao banco de dados. Tente novamente mais tarde.');
}