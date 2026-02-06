<?php

require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Acesso inválido.']);
    exit;
}

$companyName   = trim($_POST['companyName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');

if (empty($companyName) || empty($email) || empty($password) || empty($telephone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
    exit;
}

try {
    $senhaHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO enterpriseRegister (nome, email, senha, telefone) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$companyName, $email, $senhaHash, $telephone]);

    echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário. Tente novamente mais tarde.']);
    exit;
}