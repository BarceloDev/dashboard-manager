<?php
/**
 * Diagnóstico - Requisição de Teste
 * POST para este arquivo simula o que EmployeeHere.php faz
 */
header('Content-Type: application/json; charset=utf-8');

// Log completo da requisição
$debug = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => $_SERVER['REQUEST_URI'],
    'cookies' => $_COOKIE,
    'session_status' => session_status(),
    'session_started' => false,
    'post_data' => $_POST,
];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $debug['session_started'] = true;
}

$debug['session_variables'] = $_SESSION;
$debug['referer'] = $_SERVER['HTTP_REFERER'] ?? 'Nenhum';
$debug['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'Nenhum';

exit(json_encode($debug));
?>
