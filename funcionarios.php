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

// Consulta para obter a lista de funcionários, incluindo o salário
$sql_funcionarios = "SELECT cpf, nome, permissao, salario_220h FROM funcionarios";
$result_funcionarios = $conn->query($sql_funcionarios);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários</title>
    <link rel="stylesheet" href="styles.css"> <!-- Seu CSS -->
</head>
<body>
    <div class="container">
        <h1>Lista de Funcionários</h1>
        <a href="dashboard.php"><button>Voltar ao Menu Principal</button></a>
        <br><br>

        <table border="1">
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
                    // Exibe os funcionários
                    while ($funcionario = $result_funcionarios->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $funcionario['cpf'] . "</td>";
                        echo "<td>" . $funcionario['nome'] . "</td>";
                        echo "<td>" . $funcionario['permissao'] . "</td>";
                        echo "<td>R$ " . number_format($funcionario['salario_220h'], 2, ',', '.') . "</td>"; // Exibe o salário formatado
                        echo "<td>
                                <a href='editar_funcionario.php?cpf=" . $funcionario['cpf'] . "'><button>Editar</button></a>
                                <a href='excluir_funcionario.php?cpf=" . $funcionario['cpf'] . "'><button>Excluir</button></a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhum funcionário encontrado</td></tr>";
                }

                // Fecha a conexão
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
