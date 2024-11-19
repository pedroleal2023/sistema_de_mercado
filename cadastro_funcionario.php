<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexão com o banco de dados
    $conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Recebe os dados do formulário
    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Senha criptografada
    $permissao = $_POST['permissao'];
    $salario_220h = $_POST['salario_220h'];
    $horas_trabalhadas = $_POST['horas_trabalhadas'];

    // Verifica se o CPF já está cadastrado
    $verifica_cpf = "SELECT * FROM funcionarios WHERE cpf = ?";
    $stmt_verifica = $conn->prepare($verifica_cpf);
    $stmt_verifica->bind_param('s', $cpf);
    $stmt_verifica->execute();
    $result = $stmt_verifica->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('CPF já cadastrado!');window.location.href='cadastro_funcionario.php';</script>";
        exit();
    }

    // Insere o funcionário no banco de dados
    $sql = "INSERT INTO funcionarios (cpf, nome, senha, permissao, salario_220h, horas_trabalhadas) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssdi', $cpf, $nome, $senha, $permissao, $salario_220h, $horas_trabalhadas);

    if ($stmt->execute()) {
        echo "<script>alert('Funcionário cadastrado com sucesso!');window.location.href='funcionarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar funcionário!');window.location.href='cadastro_funcionario.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Funcionário</h1>

        <!-- Formulário de Cadastro de Funcionário -->
        <form method="POST" action="">
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="permissao">Permissão:</label>
            <select id="permissao" name="permissao" required>
                <option value="admin">Admin</option>
                <option value="funcionario">Funcionário</option>
            </select>

            <label for="salario_220h">Salário (220h):</label>
            <input type="number" id="salario_220h" name="salario_220h" required>

            <label for="horas_trabalhadas">Horas Trabalhadas:</label>
            <input type="number" id="horas_trabalhadas" name="horas_trabalhadas" required>

            <button type="submit">Cadastrar Funcionário</button>
        </form>

        <a href="funcionarios.php" class="back-link">Lista de Funcionários</a>
    </div>
</body>
</html>
