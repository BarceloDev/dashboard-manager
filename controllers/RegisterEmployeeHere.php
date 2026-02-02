<?php

require_once __DIR__ . '/../config/connection.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Acesso inválido.']);
    exit;
}

$nome   = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$enterpriseName = trim($_POST['enterpriseName'] ?? '');

if (empty($nome) || empty($email) || empty($senha) || empty($telefone) || empty($enterpriseName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    exit;
}

try {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no employeeRegister (pertence à empresa)
    $sql = "INSERT INTO employeeRegister (nome, email, senha, telefone, enterpriseName) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $senhaHash, $telefone, $enterpriseName]);

    echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
    exit;

} catch (PDOException $e) {
    error_log('Erro ao cadastrar funcionário: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário. Tente novamente mais tarde.']);
    exit;
}