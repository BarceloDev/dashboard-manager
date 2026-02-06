<?php
/**
 * Teste simples para debug das funções Edit e Delete
 * Acesse: /dashboard-manager/test-employee-functions.php
 */

session_start();
require_once __DIR__ . '/config/connection.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <title>Teste de Funções de Funcionário</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>Teste de Funções de Funcionário</h1>";

// Simular login de empresa
echo "<div class='test'>";
echo "<h2>1. Simulando Login de Empresa</h2>";
$_SESSION['usuario_id'] = 1; // Assumindo que existe empresa com ID 1
$stmt = $pdo->prepare('SELECT * FROM enterpriseRegister WHERE id = ?');
$stmt->execute([1]);
$empresa = $stmt->fetch();

if ($empresa) {
    $_SESSION['usuario_nome'] = $empresa['nome'];
    echo "<p class='success'>Empresa encontrada: " . htmlspecialchars($empresa['nome']) . "</p>";
    echo "<pre>" . print_r($empresa, true) . "</pre>";
} else {
    echo "<p class='error'>Nenhuma empresa com ID 1 encontrada</p>";
}
echo "</div>";

// Listar funcionários
echo "<div class='test'>";
echo "<h2>2. Listando Funcionários da Empresa</h2>";
$stmt = $pdo->prepare('SELECT * FROM employeeRegister WHERE enterpriseName = ?');
$stmt->execute([$_SESSION['usuario_nome']]);
$funcionarios = $stmt->fetchAll();

if ($funcionarios) {
    echo "<p class='success'>Total de funcionários: " . count($funcionarios) . "</p>";
    foreach ($funcionarios as $f) {
        echo "<pre>" . print_r($f, true) . "</pre>";
    }
} else {
    echo "<p class='error'>Nenhum funcionário encontrado</p>";
}
echo "</div>";

// Testar UPDATE
if ($funcionarios) {
    echo "<div class='test'>";
    echo "<h2>3. Testando UPDATE (Simular Edit)</h2>";
    $f = $funcionarios[0];
    $new_nome = $f['nome'] . ' (Editado)';
    
    try {
        $stmt = $pdo->prepare('UPDATE employeeRegister SET nome = ? WHERE id = ? AND enterpriseName = ?');
        $stmt->execute([$new_nome, $f['id'], $_SESSION['usuario_nome']]);
        echo "<p class='success'>UPDATE executado com sucesso</p>";
        
        // Desfazer a mudança
        $stmt = $pdo->prepare('UPDATE employeeRegister SET nome = ? WHERE id = ?');
        $stmt->execute([$f['nome'], $f['id']]);
    } catch (PDOException $e) {
        echo "<p class='error'>Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";
}

// Status da conexão
echo "<div class='test'>";
echo "<h2>4. Status da Conexão</h2>";
echo "<p>PDO Connection: OK</p>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<pre>";
echo "SESSION variables:\\n";
echo print_r($_SESSION, true);
echo "</pre>";
echo "</div>";

echo "</body></html>";
?>
