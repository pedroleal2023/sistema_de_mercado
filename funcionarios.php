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

// Lógica para excluir funcionário, se solicitado
if (isset($_GET['excluir_cpf'])) {
    $cpf = $_GET['excluir_cpf'];

    // Exclui o funcionário pelo CPF
    $sql_excluir = "DELETE FROM funcionarios WHERE cpf = ?";
    $stmt = $conn->prepare($sql_excluir);
    $stmt->bind_param('s', $cpf);

    if ($stmt->execute()) {
        echo "<script>alert('Funcionário excluído com sucesso!');window.location.href='funcionarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir funcionário!');window.location.href='funcionarios.php';</script>";
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Consulta para listar os funcionários
$sql_funcionarios = "SELECT cpf, nome, permissao, salario_220h FROM funcionarios";
$result_funcionarios = $conn->query($sql_funcionarios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        button {
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .btn-azul {
            background-color: #007bff; 
        }

        .btn-azul:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
            margin-right: 10px;
        }

        br {
            margin-bottom: 10px;
        }

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

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td button {
            background-color: #dc3545;
            margin-right: 10px;
        }

        td button:hover {
            background-color: #c82333;
        }

        a button {
            background-color: #28a745;
        }

        a button:hover {
            background-color: #218838;
        }

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
        <h1>Lista de Funcionários</h1>
        
        <!-- Botões de navegação -->
        <div class="button-group">
            <a href="cadastro_funcionario.php"><button class="btn-azul">Cadastrar Novo Funcionário</button></a>
            <a href="dashboard.php"><button class="btn-azul">Voltar ao Menu Principal</button></a>
        </div>

        <br><br>

        <table>
            <thead>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Permissão</th>
                    <th>Salário (220h)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_funcionarios->num_rows > 0) {
                    while ($funcionario = $result_funcionarios->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($funcionario['cpf']) . "</td>";
                        echo "<td>" . htmlspecialchars($funcionario['nome']) . "</td>";
                        echo "<td>" . htmlspecialchars($funcionario['permissao']) . "</td>";
                        echo "<td>R$ " . number_format($funcionario['salario_220h'], 2, ',', '.') . "</td>";
                        echo "<td>
                                <a href='editar_funcionario.php?cpf=" . urlencode($funcionario['cpf']) . "'><button>Editar</button></a>
                                <button onclick=\"confirmarExclusao('" . htmlspecialchars($funcionario['cpf']) . "')\">Excluir</button>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhum funcionário encontrado</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Função para confirmar a exclusão
        function confirmarExclusao(cpf) {
            if (confirm("Tem certeza que deseja excluir este funcionário?")) {
                window.location.href = `funcionarios.php?excluir_cpf=${cpf}`;
            }
        }
    </script>
</body>
</html>
