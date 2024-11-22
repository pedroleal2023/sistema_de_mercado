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
$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : null;
$qtde_venda = isset($_POST['qtde_venda']) ? $_POST['qtde_venda'] : null;
$desconto = isset($_POST['desconto']) ? $_POST['desconto'] : 0; // Desconto padrão 0 se não informado
$data_venda = date('Y-m-d H:i:s'); // Data e hora da venda

// Verificar se os dados obrigatórios foram enviados
if (!$codigo || !$qtde_venda) {
    echo "<script>alert('Código do produto e quantidade são obrigatórios!'); window.location.href='vendas.php';</script>";
    exit();
}

// Busca o produto no banco
$sql = "SELECT nome, preco, estoque FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $codigo);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();

// Verifica se o produto foi encontrado e se há estoque suficiente
if (!$produto) {
    echo "<script>alert('Produto não encontrado!'); window.location.href='vendas.php';</script>";
    exit();
}

if ($produto['estoque'] >= $qtde_venda) {
    // Calcula o valor total com base no preço e no desconto
    $preco_unitario = $produto['preco'];
    $valor_total = ($preco_unitario * $qtde_venda) * ((100 - $desconto) / 100); // Aplica o desconto

    // Atualiza o estoque do produto
    $novo_estoque = $produto['estoque'] - $qtde_venda;
    $sql_update = "UPDATE produtos SET estoque = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ii', $novo_estoque, $codigo);
    $stmt_update->execute();

    // Insere a venda no banco com a data
    $sql_venda = "INSERT INTO vendas (codigo, qtde_venda, valor, desconto, data_venda) VALUES (?, ?, ?, ?, ?)";
    $stmt_venda = $conn->prepare($sql_venda);
    $stmt_venda->bind_param('iidss', $codigo, $qtde_venda, $valor_total, $desconto, $data_venda);
    $stmt_venda->execute();

    echo "<script>alert('Venda registrada com sucesso!'); window.location.href='vendas.php';</script>";
} else {
    echo "<script>alert('Estoque insuficiente!'); window.location.href='vendas.php';</script>";
}

$stmt->close();
$conn->close();
?>
