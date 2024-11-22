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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
            font-size: 1.8em;
            color: #333;
            margin-bottom: 20px;
        }
        .section {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }
        .menu-title {
            font-size: 2.5em;
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 4px solid #007bff;
            padding-bottom: 10px;
        }
        .menu {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 40px;
        }
        .menu-item {
            font-size: 1.2em;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
            padding: 12px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-transform: uppercase;
            display: block;
            text-align: center;
            border: 2px solid transparent;
        }
        .menu-item:hover {
            background-color: #007bff;
            color: white;
            transform: scale(1.05);
            border-color: #0056b3;
        }
        .menu-item:focus {
            outline: none;
            border-color: #0056b3;
        }
        .info p {
            font-size: 1.1em;
            margin: 15px 0;
            text-align: center;
        }
        .info strong {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Menu Principal -->
        <div class="menu-title">Menu Principal</div>

        <!-- Seção do Menu -->
        <div class="section">
            <div class="menu">
                <a href="produtos.php" class="menu-item">Cadastro de Produtos</a>
                <?php if ($permissao === 'a') : ?>
                    <a href="cadastro_funcionario.php" class="menu-item">Cadastro de Funcionários</a>
                <?php endif; ?>
                <a href="vendas.php" class="menu-item">Cadastrar Venda</a>
                <a href="logout.php" class="menu-item">Sair</a>
            </div>
        </div>

        <!-- Seção de Informações de Vendas -->
        <div class="section">
    <h2>Informações de Vendas</h2>
    <div class="info">
        <p><strong>Total de Vendas Realizadas:</strong> R$ <?php echo number_format($total_vendas, 2, ',', '.'); ?></p>
        <p><strong>Total de Descontos Aplicados:</strong> R$ <?php echo number_format($total_vendas_com_desc, 2, ',', '.'); ?></p>
    </div>
        </div>
    </div>
</body>
</html>
