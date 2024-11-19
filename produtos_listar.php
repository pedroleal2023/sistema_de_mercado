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

// Consulta para pegar os produtos
$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <link rel="stylesheet" href="style.css"> <!-- Adicione o arquivo CSS -->
</head>
<body>
    <h1>Lista de Produtos</h1>
    
    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Validade</th>
                <th>Unidade de Medida</th>
                <th>Quantidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . number_format($row['preco'], 2, ',', '.') . "</td>";
                    echo "<td>" . $row['validade'] . "</td>";
                    echo "<td>" . $row['unid_medida'] . "</td>";
                    echo "<td>" . $row['quantidade'] . "</td>";
                    // Botões de ação de edição e exclusão - alterando 'id' para 'codigo'
                    echo "<td>
                            <a href='editar_produto.php?codigo=" . $row['codigo'] . "'>Editar</a> |
                            <a href='excluir_produto.php?codigo=" . $row['codigo'] . "'>Excluir</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <a href="dashboard.php">Voltar ao Dashboard</a>
</body>
</html>

<?php
$conn->close();
?>
