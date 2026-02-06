<?php
/**
 * Teste Final - Validação de Todas as Funcionalidades
 * Acesse: /dashboard-manager/final-test.php
 */

session_start();
require_once __DIR__ . '/config/connection.php';

$tests = [];

// Teste 1: Conexão banco de dados
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM employeeRegister');
    $stmt->execute();
    $tests['db_connection'] = ['status' => 'PASS', 'message' => 'Conexão OK'];
} catch (Exception $e) {
    $tests['db_connection'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Teste 2: Empresas existem
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM enterpriseRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result['total'] > 0) {
        $tests['companies_exist'] = ['status' => 'PASS', 'message' => 'Total: ' . $result['total']];
    } else {
        $tests['companies_exist'] = ['status' => 'FAIL', 'message' => 'Nenhuma empresa encontrada'];
    }
} catch (Exception $e) {
    $tests['companies_exist'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Teste 3: Funcionários existem
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM employeeRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result['total'] > 0) {
        $tests['employees_exist'] = ['status' => 'PASS', 'message' => 'Total: ' . $result['total']];
    } else {
        $tests['employees_exist'] = ['status' => 'FAIL', 'message' => 'Nenhum funcionário encontrado'];
    }
} catch (Exception $e) {
    $tests['employees_exist'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Teste 4: Vendas registradas
try {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM notesRegister');
    $stmt->execute();
    $result = $stmt->fetch();
    $tests['sales_table'] = ['status' => 'PASS', 'message' => 'Total: ' . $result['total']];
} catch (Exception $e) {
    $tests['sales_table'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Teste 5: Verificar tabela notesRegister has enterpriseName
try {
    $stmt = $pdo->prepare('SELECT * FROM notesRegister LIMIT 1');
    $stmt->execute();
    $result = $stmt->fetch();
    if (isset($result['enterpriseName'])) {
        $tests['sales_has_company'] = ['status' => 'PASS', 'message' => 'Coluna enterpriseName existe'];
    } else {
        $tests['sales_has_company'] = ['status' => 'FAIL', 'message' => 'Coluna enterpriseName não existe - execute o SQL'];
    }
} catch (Exception $e) {
    $tests['sales_has_company'] = ['status' => 'FAIL', 'message' => $e->getMessage()];
}

// Teste 6: Sessão funcionário
if (isset($_SESSION['funcionario_id'])) {
    $tests['employee_session'] = ['status' => 'PASS', 'message' => 'Logado como funcionário'];
} else {
    $tests['employee_session'] = ['status' => 'WARN', 'message' => 'Não há sessão de funcionário'];
}

// Teste 7: Sessão empresa
if (isset($_SESSION['usuario_id'])) {
    $tests['company_session'] = ['status' => 'PASS', 'message' => 'Logado como empresa'];
} else {
    $tests['company_session'] = ['status' => 'WARN', 'message' => 'Não há sessão de empresa'];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste Final - Dashboard Manager</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .test-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; margin: 8px 0; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #ccc; }
        .test-row.PASS { background: #d4edda; border-left-color: #28a745; }
        .test-row.FAIL { background: #f8d7da; border-left-color: #dc3545; }
        .test-row.WARN { background: #fff3cd; border-left-color: #ffc107; }
        .test-name { font-weight: 600; color: #333; }
        .test-status { display: inline-block; padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .test-status.PASS { background: #28a745; color: white; }
        .test-status.FAIL { background: #dc3545; color: white; }
        .test-status.WARN { background: #ffc107; color: black; }
        .test-message { color: #666; font-size: 13px; margin-top: 4px; }
        .summary { margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 6px; text-align: center; }
        .summary-text { font-size: 16px; font-weight: 600; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee; }
        .btn { display: inline-block; padding: 10px 25px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 5px; }
        .btn:hover { background: #764ba2; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>✅ Teste Final - Dashboard Manager</h1>
        <p>Validação de todas as funcionalidades</p>
    </div>

    <div class="content">
        <?php foreach ($tests as $name => $test): ?>
            <div class="test-row <?php echo $test['status']; ?>">
                <div>
                    <div class="test-name"><?php echo ucfirst(str_replace('_', ' ', $name)); ?></div>
                    <div class="test-message"><?php echo $test['message']; ?></div>
                </div>
                <div class="test-status <?php echo $test['status']; ?>"><?php echo $test['status']; ?></div>
            </div>
        <?php endforeach; ?>

        <div class="summary">
            <?php
            $passed = count(array_filter($tests, fn($t) => $t['status'] === 'PASS'));
            $total = count($tests);
            $percent = round(($passed / $total) * 100);
            echo "<div class='summary-text'>$passed / $total testes passaram ($percent%)</div>";
            ?>
        </div>
    </div>

    <div class="footer">
        <a href="/dashboard-manager/index.html" class="btn">🏠 Voltar ao Início</a>
        <a href="/dashboard-manager/test-ajax.php" class="btn">🧪 Teste AJAX</a>
        <button class="btn" onclick="location.reload()">🔄 Atualizar</button>
    </div>
</div>
</body>
</html>
