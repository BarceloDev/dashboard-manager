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
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';

if (!$id || !$nome || !$email || !$telefone) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Campos obrigatórios faltando']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Email inválido']));
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
    
    $stmt = $pdo->prepare('SELECT id FROM employeeRegister WHERE email = ? AND id != ? AND enterpriseName = ?');
    $stmt->execute([$email, $id, $enterpriseName]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        exit(json_encode(['success' => false, 'message' => 'Email já cadastrado']));
    }
    
    $stmt = $pdo->prepare('UPDATE employeeRegister SET nome = ?, email = ?, telefone = ? WHERE id = ?');
    $stmt->execute([$nome, $email, $telefone, $id]);
    
    http_response_code(200);
    exit(json_encode([
        'success' => true,
        'message' => 'Funcionário atualizado com sucesso',
        'data' => ['id' => $id, 'nome' => $nome, 'email' => $email, 'telefone' => $telefone]
    ]));
    
} catch (Exception $e) {
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]));
}
