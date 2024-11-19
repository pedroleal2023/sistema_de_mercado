<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

// Verifica a permissão do usuário
$permissao = $_SESSION['permissao'];
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
    <div class="container">
        <h1>Bem-vindo ao seu Sistema de Mercado!</h1>
        <p>Você está logado como CPF: <?php echo $_SESSION['cpf']; ?></p>

        <h2>Menu:</h2>
        <div class="menu">
            <a href="produtos.php" class="menu-btn">Cadastro de Produtos</a>
            <!-- Exibe o link de Cadastro de Funcionários apenas para admin -->
            <?php if ($permissao === 'a') : ?>
                <a href="cadastro_funcionario.php" class="menu-btn">Cadastro de Funcionários</a>
            <?php endif; ?>
            <a href="vendas.php" class="menu-btn">Cadastrar Venda</a>
            <a href="logout.php" class="menu-btn">Sair</a>
        </div>
    </div>
</body>
</html>
