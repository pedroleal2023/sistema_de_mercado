<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com o banco de dados
    $conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Captura os dados do formulário
    $codigo = $_POST['codigo'];
    $qtde_venda = $_POST['qtde_venda'];
    $desconto = $_POST['desconto'];
    $data_venda = date("Y-m-d");

    // Consulta para obter o preço e a quantidade do produto
    $sql_produto = "SELECT preco, quantidade FROM produtos WHERE codigo = ?";
    $stmt_produto = $conn->prepare($sql_produto);
    $stmt_produto->bind_param('s', $codigo);
    $stmt_produto->execute();
    $result_produto = $stmt_produto->get_result();

    if ($result_produto->num_rows > 0) {
        $produto = $result_produto->fetch_assoc();
        $preco = $produto['preco'];
        $quantidade_estoque = $produto['quantidade'];

        // Verifica se há estoque suficiente
        if ($quantidade_estoque >= $qtde_venda) {
            // Calcula o valor da venda com o desconto
            $valor_total = ($preco * $qtde_venda) * ((100 - $desconto) / 100);

            // Insere a venda no banco de dados
            $sql_venda = "INSERT INTO vendas (codigo, qtde_venda, data_venda, valor, desconto) VALUES (?, ?, ?, ?, ?)";
            $stmt_venda = $conn->prepare($sql_venda);
            $stmt_venda->bind_param('sissd', $codigo, $qtde_venda, $data_venda, $valor_total, $desconto);
            $stmt_venda->execute();

            // Atualiza a quantidade de estoque
            $nova_quantidade = $quantidade_estoque - $qtde_venda;
            $sql_atualiza_estoque = "UPDATE produtos SET quantidade = ? WHERE codigo = ?";
            $stmt_atualiza_estoque = $conn->prepare($sql_atualiza_estoque);
            $stmt_atualiza_estoque->bind_param('is', $nova_quantidade, $codigo);
            $stmt_atualiza_estoque->execute();

            echo "Venda registrada com sucesso!";
        } else {
            echo "Não há estoque suficiente para a venda!";
        }
    } else {
        echo "Produto não encontrado!";
    }

    // Fecha as conexões
    $stmt_produto->close();
    $stmt_venda->close();
    $stmt_atualiza_estoque->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Venda</title>
    <link rel="stylesheet" href="styles.css"> <!-- Seu CSS -->
</head>
<body>
    <div class="container">
        <h1>Cadastro de Venda</h1>
        <form action="vendas.php" method="POST">
            <label for="codigo">Código do Produto:</label>
            <input type="text" id="codigo" name="codigo" required>

            <label for="qtde_venda">Quantidade:</label>
            <input type="number" id="qtde_venda" name="qtde_venda" required>

            <label for="desconto">Desconto (%):</label>
            <input type="number" id="desconto" name="desconto" value="0" required>

            <button type="submit">Cadastrar Venda</button>
        </form>

        <!-- Botão de voltar ao dashboard -->
        <br><br>
        <a href="dashboard.php">
            <button type="button">Voltar ao Dashboard</button>
        </a>
    </div>
</body>
</html>
