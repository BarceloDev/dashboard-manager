<?php

ob_start();

require_once __DIR__ . '/../config/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Acesso inválido.']);
	exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Preencha todos os campos.']);
	exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Email inválido.']);
	exit;
}

try {
	$sql = "SELECT * FROM employeeRegister WHERE email = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$email]);
	$usuario = $stmt->fetch();

	if ($usuario && password_verify($password, $usuario['senha'])) {
		// Sessão para o funcionário — não sobrescreve sessão da empresa
		$_SESSION['funcionario_id'] = $usuario['id'];
		$_SESSION['funcionario_nome'] = $usuario['nome'];
		$_SESSION['funcionario_email'] = $usuario['email'];
		$_SESSION['funcionario_enterpriseName'] = $usuario['enterpriseName'];

		echo json_encode([
			'success' => true,
			'message' => 'Login do funcionário realizado com sucesso!',
			'funcionario_id' => $usuario['id'],
			'funcionario_nome' => $usuario['nome']
		]);
		exit;
	} else {
		http_response_code(401);
		echo json_encode(['success' => false, 'message' => 'E-mail ou senha inválidos.']);
		exit;
	}

} catch (PDOException $e) {
	error_log('Erro no login do funcionário: ' . $e->getMessage());
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Erro ao processar o login. Tente novamente.']);
	exit;
}

