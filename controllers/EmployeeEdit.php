<?php
require_once __DIR__ . '/../config/connection.php';
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if ($id <= 0 || $nome === '' || $email === '' || $telefone === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'E-mail inválido.']);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Não autorizado.']);
    exit;
}

try {
    // Recupera o nome da empresa a partir do usuário logado
    $stmt = $pdo->prepare('SELECT nome FROM enterpriseRegister WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $enterprise = $stmt->fetch();

    if (!$enterprise) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Empresa não encontrada.']);
        exit;
    }

    $enterpriseName = $enterprise['nome'];

    // Verifica que o funcionário pertence à empresa
    $stmt = $pdo->prepare('SELECT id FROM employeeRegister WHERE id = ? AND enterpriseName = ?');
    $stmt->execute([$id, $enterpriseName]);
    $employee = $stmt->fetch();

    if (!$employee) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Funcionário não encontrado para esta empresa.']);
        exit;
    }

    // Atualiza
    $stmt = $pdo->prepare('UPDATE employeeRegister SET nome = ?, email = ?, telefone = ? WHERE id = ?');
    $stmt->execute([$nome, $email, $telefone, $id]);

    echo json_encode(['success' => true, 'message' => 'Funcionário atualizado com sucesso.', 'data' => ['id' => $id, 'nome' => $nome, 'email' => $email, 'telefone' => $telefone]]);
    exit;

} catch (PDOException $e) {
    error_log('Erro ao atualizar funcionário: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar funcionário.']);
    exit;
}
