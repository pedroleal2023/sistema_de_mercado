<?php
// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');
;

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Captura os dados do formulário
$cpf = $_POST['cpf'];
$senha = $_POST['senha'];

// Verifica se o CPF e a senha existem no banco
$sql = "SELECT * FROM funcionarios WHERE cpf = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $cpf, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login bem-sucedido
    session_start();
    $_SESSION['cpf'] = $cpf;
    header("Location: dashboard.php"); // Redireciona para o dashboard
} else {
    // Falha no login
    echo "<script>alert('CPF ou senha incorretos!');window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
