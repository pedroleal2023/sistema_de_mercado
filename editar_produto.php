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

// Captura o código do produto para edição (alterado de 'id' para 'codigo')
$codigo = $_GET['codigo'];

// Consulta para pegar os dados do produto (alterado 'id' para 'codigo')
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

    // Atualiza o produto no banco de dados (alterado 'id' para 'codigo')
    $sql_update = "UPDATE produtos SET nome = ?, preco = ?, validade = ?, unid_medida = ?, quantidade = ? WHERE codigo = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ssdsi', $nome, $preco, $validade, $unid_medida, $quantidade, $codigo);

    if ($stmt_update->execute()) {
        echo "<script>alert('Produto atualizado com sucesso!');window.location.href='produtos_listar.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar produto!');window.location.href='produtos_listar.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="style.css"> <!-- Link para o seu arquivo CSS -->
</head>
<body>
    <h1>Editar Produto</h1>

    <form action="" method="POST">
        <label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome" value="<?php echo $product['nome']; ?>" required>

        <label for="preco">Preço:</label>
        <input type="text" id="preco" name="preco" value="<?php echo $product['preco']; ?>" required>

        <label for="validade">Validade:</label>
        <input type="date" id="validade" name="validade" value="<?php echo $product['validade']; ?>" required>

        <label for="unid_medida">Unidade de Medida:</label>
        <input type="text" id="unid_medida" name="unid_medida" value="<?php echo $product['unid_medida']; ?>" required>

        <label for="quantidade">Quantidade em Estoque:</label>
        <input type="number" id="quantidade" name="quantidade" value="<?php echo $product['quantidade']; ?>" required>

        <button type="submit">Atualizar Produto</button>
    </form>

    <a href="produtos_listar.php">Voltar para a lista de produtos</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
