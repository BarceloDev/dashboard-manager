<?php
/**
 * Visualizador de Logs do Servidor
 * Acesse: /dashboard-manager/view-logs.php
 */

session_start();
require_once __DIR__ . '/config/connection.php';

// Verifica se é administrador
$isAdmin = isset($_SESSION['usuario_id']); // Simplificado - apenas para debug

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizador de Logs</title>
    <style>
        body { font-family: Courier New, monospace; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .log-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; background: #fafafa; border-radius: 4px; }
        .log-section h2 { margin-top: 0; color: #0066cc; font-size: 16px; }
        pre { background: #2b2b2b; color: #f8f8f2; padding: 10px; border-radius: 4px; overflow-x: auto; max-height: 400px; }
        .error { color: #cc0000; }
        .success { color: #00aa00; }
        .info { color: #0066cc; }
        .button { display: inline-block; margin: 10px 0; padding: 10px 20px; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .button:hover { background: #0052a3; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Visualizador de Logs do Servidor</h1>
    
    <div class="log-section">
        <h2>Informações do Sistema</h2>
        <pre>
PHP Version: <?php echo phpversion(); ?>
Server Software: <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
Session ID: <?php echo session_id(); ?>
Usuario ID: <?php echo $_SESSION['usuario_id'] ?? 'Não autenticado'; ?>
        </pre>
    </div>

    <div class="log-section">
        <h2>Status da Conexão Banco de Dados</h2>
        <pre>
<?php
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM employeeRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "✓ Conexão OK\n";
    echo "Total de Funcionários: " . $result['total'] . "\n";
    
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM enterpriseRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Total de Empresas: " . $result['total'] . "\n";
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
?>
        </pre>
    </div>

    <div class="log-section">
        <h2>Testando Operação de UPDATE (Editar)</h2>
        <pre>
<?php
if ($_SESSION['usuario_id']) {
    try {
        // Buscar empresa e funcionário
        $stmt = $pdo->prepare('SELECT nome FROM enterpriseRegister WHERE id = ?');
        $stmt->execute([$_SESSION['usuario_id']]);
        $empresa = $stmt->fetch();
        
        if ($empresa) {
            $stmt = $pdo->prepare('SELECT id, nome FROM employeeRegister WHERE enterpriseName = ? LIMIT 1');
            $stmt->execute([$empresa['nome']]);
            $func = $stmt->fetch();
            
            if ($func) {
                echo "Teste de UPDATE:\n";
                echo "ID Funcionário: " . $func['id'] . "\n";
                echo "Nome Atual: " . $func['nome'] . "\n";
                echo "✓ Ready para teste\n";
            } else {
                echo "✗ Nenhum funcionário encontrado para testar\n";
            }
        } else {
            echo "✗ Empresa não encontrada\n";
        }
    } catch (Exception $e) {
        echo "✗ Erro: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Você precisa estar logado como empresa para ver testes\n";
}
?>
        </pre>
    </div>

    <div class="log-section">
        <h2>Arquivo de Log do PHP</h2>
        <pre>
<?php
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    $lines = file($logFile, FILE_SKIP_EMPTY_LINES);
    $recent = array_slice($lines, -20); // Últimas 20 linhas
    foreach ($recent as $line) {
        echo htmlspecialchars($line);
    }
    echo "\n\nArquivo: " . $logFile . "\n";
} else {
    echo "Arquivo de log não encontrado.\n";
    echo "error_log setting: " . ($logFile ?: 'Não definido') . "\n";
}
?>
        </pre>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <button class="button" onclick="location.reload()">🔄 Atualizar</button>
        <a href="/dashboard-manager/index.html" class="button">🏠 Voltar ao Início</a>
    </div>
</div>

<script>
// Auto-atualiza a cada 5 segundos
// setInterval(() => location.reload(), 5000);
</script>
</body>
</html>
?>
