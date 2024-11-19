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

// Consulta para listar os produtos
$sql_produtos = "SELECT codigo, nome, preco, validade, quantidade FROM produtos";
$result_produtos = $conn->query($sql_produtos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Produtos</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Função para confirmar a exclusão
        function confirmarExclusao(url) {
            if (confirm("Tem certeza que deseja excluir este produto?")) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Produtos</h1>
        <a href="cadastrar_produto.php"><button>Cadastrar Novo Produto</button></a>
        <a href="dashboard.php"><button>Voltar ao Menu Principal</button></a>
        <br><br>

        <table border="1">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Validade</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_produtos->num_rows > 0) {
                    while ($produto = $result_produtos->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $produto['codigo'] . "</td>";
                        echo "<td>" . $produto['nome'] . "</td>";
                        echo "<td>R$ " . number_format($produto['preco'], 2, ',', '.') . "</td>";
                        echo "<td>" . $produto['validade'] . "</td>";
                        echo "<td>" . $produto['quantidade'] . "</td>";
                        echo "<td>
                                <a href='editar_produto.php?codigo=" . $produto['codigo'] . "'><button>Editar</button></a>
                                <button onclick=\"confirmarExclusao('excluir_produto.php?codigo=" . $produto['codigo'] . "')\">Excluir</button>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhum produto encontrado</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
