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
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
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

    // Exclui o funcionário
    $stmt = $pdo->prepare('DELETE FROM employeeRegister WHERE id = ?');
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Funcionário removido com sucesso.']);
    exit;

} catch (PDOException $e) {
    error_log('Erro ao excluir funcionário: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir funcionário.']);
    exit;
}
