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
        echo "<script>alert('Funcionário excluído com sucesso!');window.location.href='cadastro_funcionario.php';</script>";
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
    <link rel="stylesheet" href="styles.css">
    <script>
        // Função para confirmar a exclusão
        function confirmarExclusao(cpf) {
            if (confirm("Tem certeza que deseja excluir este funcionário?")) {
                window.location.href = `funcionarios.php?excluir_cpf=${cpf}`;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Lista de Funcionários</h1>
        <a href="cadastro_funcionario.php"><button>Cadastrar Novo Funcionário</button></a>
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
                    while ($funcionario = $result_funcionarios->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $funcionario['cpf'] . "</td>";
                        echo "<td>" . $funcionario['nome'] . "</td>";
                        echo "<td>" . $funcionario['permissao'] . "</td>";
                        echo "<td>R$ " . number_format($funcionario['salario_220h'], 2, ',', '.') . "</td>";
                        echo "<td>
                                <a href='editar_funcionario.php?cpf=" . $funcionario['cpf'] . "'><button>Editar</button></a>
                                <button onclick=\"confirmarExclusao('" . $funcionario['cpf'] . "')\">Excluir</button>
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
</body>
</html>
