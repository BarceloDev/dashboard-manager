<?php
ob_start();

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir apenas as variáveis de sessão do funcionário
unset($_SESSION['funcionario_id']);
unset($_SESSION['funcionario_nome']);
unset($_SESSION['funcionario_email']);
unset($_SESSION['funcionario_enterpriseName']);

exit(json_encode([
    'success' => true,
    'message' => 'Logout realizado com sucesso!'
]));
