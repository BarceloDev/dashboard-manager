<?php

ob_start();

require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Método inválido']));
}

// Verifica autenticação do funcionário
if (!isset($_SESSION['funcionario_id']) || !isset($_SESSION['funcionario_nome']) || !isset($_SESSION['funcionario_enterpriseName'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Funcionário não autenticado. Faça login novamente.']));
}

$cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : '';
$produto = isset($_POST['produto']) ? trim($_POST['produto']) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

// Validações
if (empty($cliente) || strlen($cliente) < 2) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Nome do cliente inválido (mínimo 2 caracteres).']));
}

if (empty($produto) || strlen($produto) < 2) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Nome do produto inválido (mínimo 2 caracteres).']));
}

if ($price <= 0) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Preço deve ser maior que zero.']));
}

if ($price > 999999) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Preço excede o limite permitido.']));
}

$vendedor = $_SESSION['funcionario_nome'];
$enterpriseName = $_SESSION['funcionario_enterpriseName'];

try {
    // Verificar se o funcionário ainda existe no banco
    $stmt = $pdo->prepare('SELECT id FROM employeeRegister WHERE id = ? AND enterpriseName = ?');
    $stmt->execute([$_SESSION['funcionario_id'], $enterpriseName]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        exit(json_encode(['success' => false, 'message' => 'Funcionário não encontrado no sistema.']));
    }
    
    // Inserir venda
    $sql = "INSERT INTO notesRegister (vendedor, cliente, produto, price, enterpriseName) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vendedor, $cliente, $produto, $price, $enterpriseName]);

    http_response_code(201);
    exit(json_encode(['success' => true, 'message' => 'Venda registrada com sucesso!']));

} catch (PDOException $e) {
    error_log('Erro ao registrar venda: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao registrar venda no banco de dados.']));
} catch (Exception $e) {
    error_log('Erro geral ao registrar venda: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao processar sua requisição.']));
}
