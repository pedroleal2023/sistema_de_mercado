<?php
// Inicia a sessão
session_start();

// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Captura os dados do formulário
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

// Prepara e executa a consulta SQL para validar o login
$sql = "SELECT cpf, permissao FROM funcionarios WHERE cpf = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $cpf, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login bem-sucedido
    $usuario = $result->fetch_assoc();
    $_SESSION['cpf'] = $usuario['cpf']; // Armazena o CPF na sessão
    $_SESSION['permissao'] = $usuario['permissao']; // Armazena a permissão na sessão

    // Redireciona para o dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // Login falhou
    echo "<script>alert('CPF ou senha incorretos!'); window.location.href='login.html';</script>";
}

// Fecha a conexão
$stmt->close();
$conn->close();
