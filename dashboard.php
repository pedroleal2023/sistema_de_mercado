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
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Se você tiver um CSS para o Dashboard -->
</head>
<body>
    <h1>Bem-vindo ao Sistema de Mercado!</h1>
    <p>Você está logado como CPF: <?php echo $_SESSION['cpf']; ?></p>

    <h2>Menu:</h2>
    <ul>
        <li><a href="produtos.php">Cadastro de Produtos</a></li>
        <li><a href="funcionarios.php">Cadastro de Funcionários</a></li>
        <li><a href="vendas.php">Cadastrar Venda</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</body>
</html>
