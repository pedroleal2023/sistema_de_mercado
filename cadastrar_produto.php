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

// Captura os dados do formulário
$nome = $_POST['nome'];
$preco = $_POST['preco'];
$validade = $_POST['validade'];
$unid_medida = $_POST['unid_medida'];
$quantidade = $_POST['quantidade'];

// Insere os dados na tabela de produtos
$sql = "INSERT INTO produtos (nome, preco, validade, unid_medida, quantidade) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssdsi', $nome, $preco, $validade, $unid_medida, $quantidade);

if ($stmt->execute()) {
    echo "<script>alert('Produto cadastrado com sucesso!');window.location.href='produtos.php';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar produto!');window.location.href='produtos.php';</script>";
}

$stmt->close();
$conn->close();
?>
