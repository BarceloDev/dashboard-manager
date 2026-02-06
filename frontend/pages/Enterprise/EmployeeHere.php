<?php

require_once __DIR__ . '/../../../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /dashboard-manager/index.html');
    exit;
}

// Recupera nome da empresa do usuário logado
$stmt = $pdo->prepare('SELECT nome FROM enterpriseRegister WHERE id = ?');
$stmt->execute([$_SESSION['usuario_id']]);
$enterprise = $stmt->fetch();
$enterpriseName = $enterprise['nome'] ?? '';

// Busca funcionários desta empresa
$stmt = $pdo->prepare('SELECT id, nome, email, telefone FROM employeeRegister WHERE enterpriseName = ?');
$stmt->execute([$enterpriseName]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="../../style/global.css">
    <link rel="stylesheet" href="../../style/EnterpriseScreen.css">
    <title>Funcionários - <?php echo htmlspecialchars($enterpriseName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></title>
    <style>
        .employee-row { display:flex; justify-content:space-between; align-items:center; padding:8px; border-bottom:1px solid #eee; }
        .employee-data { flex:1; }
        .employee-actions { width:160px; text-align:right; }
        .btn { padding:6px 10px; margin-left:6px; cursor:pointer; border:none; border-radius:4px; }
        .btn-danger{ background:#dc3545; color:#fff; }
        .btn-primary{ background:#0d6efd; color:#fff; }
        /* Modal styles */
        .modal-overlay { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.5); z-index:1000; }
        .modal { background:#fff; padding:20px; border-radius:6px; width:320px; max-width:90%; box-shadow:0 8px 24px rgba(0,0,0,0.2); }
        .modal h3 { margin-top:0; }
        .modal input { width:100%; padding:8px; margin:6px 0; box-sizing:border-box; }
        .modal .modal-actions { text-align:right; margin-top:8px; }
        .modal .alert { display:none; margin-bottom:8px; color:#721c24; background:#f8d7da; padding:8px; border-radius:4px; }
        .box {flex-wrap: wrap;}
    </style>
</head>
<body>
<div class="container py-4">
    <h2><a href="../EnterpriseScreen.html"><i class="bi bi-arrow-left"></i></a> Funcionários de <?php echo htmlspecialchars($enterpriseName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h2>
    <div class="box" id="employees-list">
        <?php if (empty($employees)): ?>
            <div class="alert">Nenhum funcionário registrado para esta empresa.</div>
        <?php else: ?>
            <?php foreach ($employees as $emp): ?>
                <div class="employee-row" id="employee-row-<?php echo $emp['id']; ?>">
                    <div class="employee-data">
                        <strong class="emp-name"><?php echo htmlspecialchars($emp['nome'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></strong>
                        <div class="emp-email"><?php echo htmlspecialchars($emp['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
                        <div class="emp-phone"><?php echo htmlspecialchars($emp['telefone'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
                    </div>
                    <div class="employee-actions">
                        <button class="btn btn-primary" onclick="openEditModal(<?php echo $emp['id']; ?>)"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-danger" onclick="deleteEmployee(<?php echo $emp['id']; ?>)"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
async function deleteEmployee(id) {
    if (!confirm('Confirma exclusão deste funcionário?')) return;
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const res = await fetch('../../../controllers/EmployeeDelete.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        
        const responseText = await res.text();
        console.log('Delete response status:', res.status);
        console.log('Delete response body:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            alert('Erro ao processar resposta do servidor: ' + responseText);
            return;
        }
        
        if (data.success) {
            const el = document.getElementById('employee-row-' + id);
            if (el) el.remove();
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Erro ao excluir.');
        }
    } catch (err) {
        console.error('Erro:', err);
        alert('Erro ao excluir funcionário: ' + err.message);
    }
}
function openEditModal(id) {
    const row = document.getElementById('employee-row-' + id);
    if (!row) return;
    const nome = row.querySelector('.emp-name').textContent.trim();
    const email = row.querySelector('.emp-email').textContent.trim();
    const telefone = row.querySelector('.emp-phone').textContent.trim();

    document.getElementById('modal-id').value = id;
    document.getElementById('modal-nome').value = nome;
    document.getElementById('modal-email').value = email;
    document.getElementById('modal-telefone').value = telefone;
    document.getElementById('modal-error').style.display = 'none';
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

async function submitEditModal(event) {
    event.preventDefault();
    const id = document.getElementById('modal-id').value;
    const nome = document.getElementById('modal-nome').value.trim();
    const email = document.getElementById('modal-email').value.trim();
    const telefone = document.getElementById('modal-telefone').value.trim();
    const errEl = document.getElementById('modal-error');

    if (!nome || !email || !telefone) { errEl.textContent = 'Preencha todos os campos.'; errEl.style.display = 'block'; return; }

    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('nome', nome);
        formData.append('email', email);
        formData.append('telefone', telefone);

        const res = await fetch('../../../controllers/EmployeeEdit.php', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        
        const responseText = await res.text();
        console.log('Edit response status:', res.status);
        console.log('Edit response body:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            errEl.textContent = 'Erro ao processar resposta: ' + responseText.substring(0, 100);
            errEl.style.display = 'block';
            return;
        }
        
        if (data.success) {
            const row = document.getElementById('employee-row-' + id);
            row.querySelector('.emp-name').textContent = data.data.nome;
            row.querySelector('.emp-email').textContent = data.data.email;
            row.querySelector('.emp-phone').textContent = data.data.telefone;
            closeEditModal();
            alert(data.message);
            location.reload();
        } else {
            errEl.textContent = data.message || 'Erro ao atualizar.';
            errEl.style.display = 'block';
        }
    } catch (err) {
        console.error('Erro ao atualizar:', err);
        errEl.textContent = 'Erro ao atualizar funcionário: ' + err.message;
        errEl.style.display = 'block';
    }
}
</script>

<!-- Modal -->
<div id="edit-modal" class="modal-overlay" role="dialog" aria-modal="true">
  <div class="modal">
    <h3>Editar Funcionário</h3>
    <div id="modal-error" class="alert"></div>
    <form id="edit-modal-form" onsubmit="submitEditModal(event)">
      <input type="hidden" id="modal-id" name="id" />
      <input type="text" id="modal-nome" name="nome" placeholder="Nome" required />
      <input type="email" id="modal-email" name="email" placeholder="E-mail" required />
      <input type="text" id="modal-telefone" name="telefone" placeholder="Telefone" required />
      <div class="modal-actions">
        <button type="button" class="btn" onclick="closeEditModal()">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
