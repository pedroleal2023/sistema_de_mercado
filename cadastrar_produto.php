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
        // Validação e conversão da data
        $data_formatada = DateTime::createFromFormat('d/m/Y', $validade);
        if ($data_formatada) {
            $validade_banco = $data_formatada->format('Y-m-d');
        } else {
            echo "<script>alert('Data de validade inválida! Use o formato dd/mm/yyyy.');window.location.href='cadastrar_produto.php';</script>";
            exit();
        }

        // Insere os dados na tabela de produtos
        $sql = "INSERT INTO produtos (nome, preco, validade, unid_medida, quantidade) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdsi', $nome, $preco, $validade_banco, $unid_medida, $quantidade);

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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
        }

        input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cadastrar Produto</h1>
        <form action="cadastrar_produto.php" method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" step="0.01" required>

            <label for="validade">Validade (dd/mm/aaaa):</label>
            <input type="text" id="validade" name="validade" required>

            <label for="unid_medida">Unidade de Medida:</label>
            <input type="text" id="unid_medida" name="unid_medida" placeholder="Exemplo: kg, g, unidades" required>

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" required>

            <div class="button-container">
                <button type="submit">Cadastrar Produto</button>
                <a href="produtos.php">Voltar à lista de produtos</a>
            </div>
        </form>
    </div>
</body>
</html>
