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
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Dados do formulário
$codigo = $_POST['codigo']; // ID do produto
$qtde_venda = $_POST['qtde_venda'];
$valor = $_POST['valor'];
$desconto = $_POST['desconto'];

// Busca o produto no banco
$sql = "SELECT nome, preco, estoque FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $codigo);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

// Verifica se há estoque suficiente
if ($produto['estoque'] >= $qtde_venda) {
    // Atualiza o estoque do produto
    $novo_estoque = $produto['estoque'] - $qtde_venda;
    $sql_update = "UPDATE produtos SET estoque = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ii', $novo_estoque, $codigo);
    $stmt_update->execute();

    // Insere a venda no banco
    $sql_venda = "INSERT INTO vendas (codigo, qtde_venda, valor, desconto) VALUES (?, ?, ?, ?)";
    $stmt_venda = $conn->prepare($sql_venda);
    $stmt_venda->bind_param('iidi', $codigo, $qtde_venda, $valor, $desconto);
    $stmt_venda->execute();

    echo "<script>alert('Venda registrada com sucesso!'); window.location.href='vendas.php';</script>";
} else {
    echo "<script>alert('Estoque insuficiente!'); window.location.href='vendas.php';</script>";
}

$stmt->close();
$conn->close();
?>
