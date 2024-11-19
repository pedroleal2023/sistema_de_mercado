<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link para o arquivo CSS externo -->
</head>
<body>
    <h1>Cadastro de Produtos</h1>
    
    <div class="container">
        <!-- Formulário de cadastro de produtos -->
        <form action="cadastrar_produto.php" method="POST">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="preco">Preço:</label>
            <input type="text" id="preco" name="preco" required>

            <label for="validade">Validade:</label>
            <input type="date" id="validade" name="validade" required>

            <label for="unid_medida">Unidade de Medida:</label>
            <input type="text" id="unid_medida" name="unid_medida" required>

            <label for="quantidade">Quantidade em Estoque:</label>
            <input type="number" id="quantidade" name="quantidade" required>

            <button type="submit">Cadastrar Produto</button>
        </form>
    </div>

    <a href="dashboard.php">Voltar ao Dashboard</a>
</body>
</html>
