<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Captura o código do produto para exclusão (alterado de 'id' para 'codigo')
$codigo = $_GET['codigo'];

// Deleta o produto no banco de dados (alterado 'id' para 'codigo')
$sql = "DELETE FROM produtos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $codigo);

if ($stmt->execute()) {
    echo "<script>alert('Produto excluído com sucesso!');window.location.href='produtos_listar.php';</script>";
} else {
    echo "<script>alert('Erro ao excluir produto!');window.location.href='produtos_listar.php';</script>";
}

$stmt->close();
$conn->close();
?>
