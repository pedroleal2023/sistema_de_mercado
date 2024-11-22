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

// Captura o código do produto para edição
$codigo = $_GET['codigo'];

// Consulta para pegar os dados do produto
$sql = "SELECT * FROM produtos WHERE codigo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $codigo);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados atualizados do formulário
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $validade = $_POST['validade'];
    $unid_medida = $_POST['unid_medida'];
    $quantidade = $_POST['quantidade'];

    // Atualiza o produto no banco de dados
    $sql_update = "UPDATE produtos SET nome = ?, preco = ?, validade = ?, unid_medida = ?, quantidade = ? WHERE codigo = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('sdssii', $nome, $preco, $validade, $unid_medida, $quantidade, $codigo);

    if ($stmt_update->execute()) {
        echo "<script>alert('Produto atualizado com sucesso!');window.location.href='produtos.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar produto!');window.location.href='produtos.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
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

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
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
        <h1>Editar Produto</h1>

        <form action="" method="POST">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($product['nome']); ?>" required>

            <label for="preco">Preço:</label>
            <input type="text" id="preco" name="preco" value="<?php echo htmlspecialchars($product['preco']); ?>" required>

            <label for="validade">Validade:</label>
            <input type="date" id="validade" name="validade" value="<?php echo htmlspecialchars($product['validade']); ?>" required>

            <label for="unid_medida">Unidade de Medida:</label>
            <input type="text" id="unid_medida" name="unid_medida" value="<?php echo htmlspecialchars($product['unid_medida']); ?>" required>

            <label for="quantidade">Quantidade em Estoque:</label>
            <input type="number" id="quantidade" name="quantidade" value="<?php echo htmlspecialchars($product['quantidade']); ?>" required>

            <button type="submit">Atualizar Produto</button>
        </form>

        <a href="produtos.php">Voltar para a lista de produtos</a>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
