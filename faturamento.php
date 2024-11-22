<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html");
    exit();
}

// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Datas padrão
$data_inicio = isset($_POST['data_inicio']) ? $_POST['data_inicio'] : date('Y-m-01'); // Primeiro dia do mês
$data_fim = isset($_POST['data_fim']) ? $_POST['data_fim'] : date('Y-m-d'); // Hoje

// Consulta SQL para calcular o faturamento
$sql = "SELECT SUM(valor) AS faturamento_total FROM vendas WHERE data_venda BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $data_inicio, $data_fim);
$stmt->execute();
$result = $stmt->get_result();
$faturamento = $result->fetch_assoc()['faturamento_total'] ?? 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Faturamento</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Relatório de Faturamento</h1>
        <form method="POST" action="faturamento.php">
            <label for="data_inicio">Data Início:</label>
            <input type="date" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>" required>
            
            <label for="data_fim">Data Fim:</label>
            <input type="date" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>" required>
            
            <button type="submit">Gerar Relatório</button>
        </form>
        
        <h2>Faturamento Total: R$ <?php echo number_format($faturamento, 2, ',', '.'); ?></h2>
    </div>
</body>
</html>
