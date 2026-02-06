<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Teste simples
exit(json_encode([
    'success' => true,
    'message' => 'GetSalesHistory.php está acessível!',
    'funcionario_nome' => $_SESSION['funcionario_nome'] ?? 'Não definido',
    'funcionario_id' => $_SESSION['funcionario_id'] ?? 'Não definido'
]));
