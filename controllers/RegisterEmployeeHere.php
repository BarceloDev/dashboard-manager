<?php

ob_start();

require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Acesso inválido.']));
}

$nome   = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$enterpriseName = trim($_POST['enterpriseName'] ?? '');

if (empty($nome) || empty($email) || empty($senha) || empty($telefone) || empty($enterpriseName)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Preencha todos os campos.']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Email inválido.']));
}

if (strlen($senha) < 3) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Senha deve ter pelo menos 3 caracteres.']));
}

if (strlen($nome) < 2) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Nome deve ter pelo menos 2 caracteres.']));
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Você precisa estar autenticado como empresa para cadastrar funcionários.']));
}

try {
    // Verificar se email já existe
    $stmt = $pdo->prepare('SELECT id FROM employeeRegister WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        exit(json_encode(['success' => false, 'message' => 'Email já cadastrado no sistema.']));
    }

    // Verificar se empresa existe e se o usuário pertence a ela
    $stmt = $pdo->prepare('SELECT id FROM enterpriseRegister WHERE nome = ?');
    $stmt->execute([$_SESSION['usuario_nome']]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        exit(json_encode(['success' => false, 'message' => 'Empresa não encontrada ou não autorizada.']));
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no employeeRegister (pertence à empresa)
    $sql = "INSERT INTO employeeRegister (nome, email, senha, telefone, enterpriseName) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $senhaHash, $telefone, $enterpriseName]);

    http_response_code(201);
    exit(json_encode(['success' => true, 'message' => 'Funcionário cadastrado com sucesso!']));

} catch (PDOException $e) {
    error_log('Erro ao cadastrar funcionário: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao cadastrar funcionário. Tente novamente mais tarde.']));
} catch (Exception $e) {
    error_log('Erro geral ao cadastrar funcionário: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao processar sua requisição.']));
}