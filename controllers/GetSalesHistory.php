<?php

ob_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticação
if (!isset($_SESSION['funcionario_id']) || !isset($_SESSION['funcionario_nome'])) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Não autenticado']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Método inválido']));
}

try {
    $vendedor = $_SESSION['funcionario_nome'];
    
    // Buscar todas as vendas do funcionário
    $sql = "SELECT id, cliente, produto, price 
            FROM notesRegister 
            WHERE vendedor = ? 
            ORDER BY id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vendedor]);
    $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totais
    $totalVendas = count($vendas);
    $valorTotal = 0;
    
    foreach ($vendas as $venda) {
        $valorTotal += $venda['price'];
    }
    
    exit(json_encode([
        'success' => true,
        'vendas' => $vendas,
        'totalVendas' => $totalVendas,
        'valorTotal' => $valorTotal
    ]));
    
} catch (PDOException $e) {
    error_log('Erro ao buscar vendas: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao buscar vendas']));
} catch (Exception $e) {
    error_log('Erro geral ao buscar vendas: ' . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Erro ao processar requisição']));
}

