<?php
require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Método inválido']));
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if (!$id) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'ID inválido']));
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Não autenticado']));
}

try {
    $stmt = $pdo->prepare('SELECT nome FROM enterpriseRegister WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $empresa = $stmt->fetch();
    
    if (!$empresa) {
        http_response_code(404);
        exit(json_encode(['success' => false, 'message' => 'Empresa não encontrada']));
    }
    
    $enterpriseName = $empresa['nome'];
    
    $stmt = $pdo->prepare('SELECT id FROM employeeRegister WHERE id = ? AND enterpriseName = ?');
    $stmt->execute([$id, $enterpriseName]);
    
    if (!$stmt->fetch()) {
        http_response_code(404);
        exit(json_encode(['success' => false, 'message' => 'Funcionário não encontrado']));
    }
    
    $stmt = $pdo->prepare('DELETE FROM employeeRegister WHERE id = ?');
    $stmt->execute([$id]);
    
    http_response_code(200);
    exit(json_encode(['success' => true, 'message' => 'Funcionário removido com sucesso']));
    
} catch (Exception $e) {
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]));
}
