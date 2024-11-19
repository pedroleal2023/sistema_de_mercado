<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

// Verifica a permissão do usuário
$permissao = $_SESSION['permissao'];

// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Consulta para obter o total de vendas realizadas
$sql_total_vendas = "SELECT SUM(valor) AS total_vendas FROM vendas";
$result_total_vendas = $conn->query($sql_total_vendas);
$row_total_vendas = $result_total_vendas->fetch_assoc();
$total_vendas = $row_total_vendas['total_vendas'];

// Consulta para obter o total de vendas com desconto
$sql_total_vendas_com_desc = "SELECT SUM(valor) AS total_vendas_com_desc FROM vendas WHERE desconto > 0";
$result_total_vendas_com_desc = $conn->query($sql_total_vendas_com_desc);
$row_total_vendas_com_desc = $result_total_vendas_com_desc->fetch_assoc();
$total_vendas_com_desc = $row_total_vendas_com_desc['total_vendas_com_desc'];

// Fecha a conexão com o banco
$conn->close();
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

        <h2>Informações de Vendas:</h2>
        <div class="vendas-info">
            <p><strong>Total de Vendas Realizadas:</strong> R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></p>
            <p><strong>Total de Vendas com Desconto:</strong> R$ <?php echo number_format($total_vendas_com_desc, 2, ',', '.'); ?></p>
        </div>
    </div>
</body>
</html>
