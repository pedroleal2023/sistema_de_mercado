<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

// Verifica se foi fornecido um CPF para edição
if (!isset($_GET['cpf'])) {
    echo "<script>alert('CPF não fornecido!');window.location.href='funcionarios.php';</script>";
    exit();
}

$cpf = $_GET['cpf'];

// Conexão com o banco de dados
$conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Busca os dados do funcionário para edição
$sql = "SELECT * FROM funcionarios WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $cpf);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Funcionário não encontrado!');window.location.href='funcionarios.php';</script>";
    exit();
}

$funcionario = $result->fetch_assoc();

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $permissao = $_POST['permissao'];
    $salario_220h = $_POST['salario_220h'];
    $horas_trabalhadas = $_POST['horas_trabalhadas'];

    // Atualiza os dados do funcionário
    $sql_update = "UPDATE funcionarios SET nome = ?, permissao = ?, salario_220h = ?, horas_trabalhadas = ? WHERE cpf = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('ssdis', $nome, $permissao, $salario_220h, $horas_trabalhadas, $cpf);

    if ($stmt_update->execute()) {
        echo "<script>alert('Funcionário atualizado com sucesso!');window.location.href='funcionarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar funcionário!');</script>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Funcionário</title>
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
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
        }

        input, select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-link {
            text-align: center;
            display: block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Funcionário</h1>

        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($funcionario['nome']); ?>" required>

            <label for="permissao">Permissão:</label>
            <select id="permissao" name="permissao" required>
                <option value="admin" <?= $funcionario['permissao'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="funcionario" <?= $funcionario['permissao'] === 'funcionario' ? 'selected' : ''; ?>>Funcionário</option>
            </select>

            <label for="salario_220h">Salário (220h):</label>
            <input type="number" id="salario_220h" name="salario_220h" step="0.01" value="<?= htmlspecialchars($funcionario['salario_220h']); ?>" required>

            <label for="horas_trabalhadas">Horas Trabalhadas:</label>
            <input type="number" id="horas_trabalhadas" name="horas_trabalhadas" value="<?= htmlspecialchars($funcionario['horas_trabalhadas']); ?>" required>

            <button type="submit">Salvar Alterações</button>
        </form>

        <a href="funcionarios.php" class="back-link">Voltar para Lista de Funcionários</a>
    </div>
</body>
</html>
