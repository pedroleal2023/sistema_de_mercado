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
    <style>
        /* Adicionando uma estilização mais suave ao corpo */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* Container geral para a tabela */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Título da página */
        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        /* Estilo dos botões */
        button {
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        /* Botões com a mesma cor azul */
        .btn-azul {
            background-color: #007bff; /* Azul suave */
        }

        .btn-azul:hover {
            background-color: #0056b3; /* Tom mais escuro de azul */
        }

        /* Botões de ação na página */
        a {
            text-decoration: none;
            margin-right: 10px;
        }

        br {
            margin-bottom: 10px;
        }

        /* Tabela de produtos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1.1em;
        }

        /* Cabeçalho da tabela */
        th {
            background-color: #007bff;
            color: white;
        }

        /* Linhas da tabela */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Coluna de Ações com maior espaçamento */
        td button {
            background-color: #dc3545;
            margin-right: 10px;
        }

        td button:hover {
            background-color: #c82333;
        }

        /* Estilo para o link de edição */
        a button {
            background-color: #28a745;
        }

        a button:hover {
            background-color: #218838;
        }

        /* Estilos para o grupo de botões acima da tabela */
        .button-group {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .button-group a {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Produtos</h1>
        
        <!-- Alinhando os botões -->
        <div class="button-group">
            <a href="cadastrar_produto.php"><button class="btn-azul">Cadastrar Novo Produto</button></a>
            <a href="dashboard.php"><button class="btn-azul">Voltar ao Menu Principal</button></a>
        </div>

        <br><br>

        <table>
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
                        // Converte a validade para o formato brasileiro (d/m/Y)
                        $validade = DateTime::createFromFormat('Y-m-d', $produto['validade']);
                        $validadeFormatada = $validade ? $validade->format('d/m/Y') : 'Data inválida';

                        echo "<tr>";
                        echo "<td>" . $produto['codigo'] . "</td>";
                        echo "<td>" . $produto['nome'] . "</td>";
                        echo "<td>R$ " . number_format($produto['preco'], 2, ',', '.') . "</td>";
                        echo "<td>" . $validadeFormatada . "</td>";
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

    <script>
        // Função para confirmar a exclusão
        function confirmarExclusao(url) {
            if (confirm("Tem certeza que deseja excluir este produto?")) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
