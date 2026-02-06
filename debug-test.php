<?php
/**
 * Arquivo de teste para debug dos controladores
 * Acesse: /dashboard-manager/debug-test.php
 */

session_start();
require_once __DIR__ . '/config/connection.php';

echo "<h2>Debug de Sessão</h2>";
echo "<pre>";
echo "Session usuario_id: " . ($_SESSION['usuario_id'] ?? 'NÃO DEFINIDO') . "\n";
echo "Session usuario_nome: " . ($_SESSION['usuario_nome'] ?? 'NÃO DEFINIDO') . "\n";
echo "</pre>";

echo "<h2>Teste de Conexão ao Banco</h2>";
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM employeeRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Total de funcionários: " . $result['total'] . "</p>";
} catch (Exception $e) {
    echo "<p>Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>Teste de Fetch com POST</h2>";
echo "<form method='post'>
    <input type='text' name='test_id' placeholder='ID do funcionário' value='1'>
    <input type='submit' value='Testar DELETE'>
</form>";

if ($_POST) {
    echo "<p>POST recebido: ID = " . ($_POST['test_id'] ?? 'nenhum') . "</p>";
}

echo "<p><a href='/dashboard-manager/index.html'>Voltar ao início</a></p>";
?>
