<?php
/**
 * Teste de Requisições AJAX
 * Acesse: /dashboard-manager/test-ajax.php
 */

session_start();
require_once __DIR__ . '/config/connection.php';

// Se houver requisição POST de teste
if ($_POST && $_POST['action'] === 'test_delete') {
    header('Content-Type: application/json; charset=utf-8');
    
    $_SESSION['usuario_id'] = intval($_POST['empresa_id'] ?? 1);
    
    // Simular chamada DELETE
    $_POST['id'] = intval($_POST['func_id'] ?? 1);
    
    // Incluir o controlador
    include __DIR__ . '/controllers/EmployeeDelete.php';
    exit;
}

if ($_POST && $_POST['action'] === 'test_edit') {
    header('Content-Type: application/json; charset=utf-8');
    
    $_SESSION['usuario_id'] = intval($_POST['empresa_id'] ?? 1);
    
    // Simular chamada EDIT
    $_POST['id'] = intval($_POST['func_id'] ?? 1);
    $_POST['nome'] = trim($_POST['nome'] ?? 'Teste');
    $_POST['email'] = trim($_POST['email'] ?? 'teste@test.com');
    $_POST['telefone'] = trim($_POST['telefone'] ?? '1234567890');
    
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Incluir o controlador
    include __DIR__ . '/controllers/EmployeeEdit.php';
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste de AJAX</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        h2 { color: #0066cc; margin-top: 0; }
        .form-group { margin: 10px 0; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { padding: 8px; width: 100%; box-sizing: border-box; }
        button { padding: 10px 20px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0052a3; }
        .result { margin-top: 15px; padding: 10px; background: #f9f9f9; border-left: 4px solid #0066cc; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        pre { background: #2b2b2b; color: #f8f8f2; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .info { background: #e7f3ff; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>🧪 Teste de Operações AJAX</h1>
    
    <div class="info">
        <strong>ℹ️ Como usar:</strong> Selecione uma empresa e um funcionário, depois clique em "Testar DELETE" ou "Testar EDIT"
    </div>

    <?php
    // Buscar empresas e funcionários
    $stmt = $pdo->prepare('SELECT id, nome FROM enterpriseRegister');
    $stmt->execute();
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare('SELECT id, nome, email, telefone, enterpriseName FROM employeeRegister');
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($empresas)) {
        echo '<div class="section" style="background: #fff3cd; border-color: #ffc107; color: #856404;">';
        echo '⚠️ Nenhuma empresa registrada no banco. Faça login primeiro.';
        echo '</div>';
    }
    ?>

    <div class="section">
        <h2>Teste DELETE (Excluir Funcionário)</h2>
        <form method="post">
            <input type="hidden" name="action" value="test_delete">
            
            <div class="form-group">
                <label>Empresa:</label>
                <select name="empresa_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($empresas as $e): ?>
                        <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Funcionário:</label>
                <select name="func_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($funcionarios as $f): ?>
                        <option value="<?php echo $f['id']; ?>">
                            <?php echo htmlspecialchars($f['nome']) . ' (' . htmlspecialchars($f['enterpriseName']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">🗑️ Testar DELETE</button>
        </form>
    </div>

    <div class="section">
        <h2>Teste EDIT (Editar Funcionário)</h2>
        <form method="post">
            <input type="hidden" name="action" value="test_edit">
            
            <div class="form-group">
                <label>Empresa:</label>
                <select name="empresa_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($empresas as $e): ?>
                        <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Funcionário:</label>
                <select name="func_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($funcionarios as $f): ?>
                        <option value="<?php echo $f['id']; ?>">
                            <?php echo htmlspecialchars($f['nome']) . ' (' . htmlspecialchars($f['enterpriseName']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Novo Nome:</label>
                <input type="text" name="nome" value="Nome Editado" required>
            </div>

            <div class="form-group">
                <label>Novo Email:</label>
                <input type="email" name="email" value="novoEmail@test.com" required>
            </div>

            <div class="form-group">
                <label>Novo Telefone:</label>
                <input type="text" name="telefone" value="9999999999" required>
            </div>

            <button type="submit">✏️ Testar EDIT</button>
        </form>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <a href="/dashboard-manager/index.html" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">🏠 Voltar</a>
    </div>
</div>
</body>
</html>
