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

// Processa os dados do formulário apenas se a requisição for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário com validação básica
    $nome = $_POST['nome'] ?? null;
    $preco = $_POST['preco'] ?? null;
    $validade = $_POST['validade'] ?? null;
    $unid_medida = $_POST['unid_medida'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;

    // Verifica se todos os campos foram preenchidos
    if ($nome && $preco && $validade && $unid_medida && $quantidade) {
        // Insere os dados na tabela de produtos
        $sql = "INSERT INTO produtos (nome, preco, validade, unid_medida, quantidade) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdsi', $nome, $preco, $validade, $unid_medida, $quantidade);

        if ($stmt->execute()) {
            echo "<script>alert('Produto cadastrado com sucesso!');window.location.href='produtos.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar produto!');window.location.href='cadastrar_produto.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Preencha todos os campos!');window.location.href='cadastrar_produto.php';</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Cadastrar Novo Produto</h1>
        <form action="cadastrar_produto.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required>
            <br>

            <label for="preco">Preço:</label>
            <input type="number" step="0.01" name="preco" id="preco" required>
            <br>

            <label for="validade">Validade:</label>
            <input type="date" name="validade" id="validade" required>
            <br>

            <label for="unid_medida">Unidade de Medida:</label>
            <input type="text" name="unid_medida" id="unid_medida" required>
            <br>

            <label for="quantidade">Quantidade:</label>
            <input type="number" name="quantidade" id="quantidade" required>
            <br><br>

            <button type="submit">Cadastrar</button>
        </form>
        <br>
        <a href="produtos.php"><button>Voltar</button></a>
    </div>
</body>
</html>
