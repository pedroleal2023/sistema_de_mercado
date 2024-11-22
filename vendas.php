<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['cpf'])) {
    header("Location: login.html"); // Redireciona para o login se não estiver logado
    exit();
}

$mensagem = ''; // Variável para armazenar a mensagem de sucesso ou erro

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexão com o banco de dados
    $conn = new mysqli('127.0.0.1:3307', 'root', '', 'sistema_mercado');

    // Verifica se houve erro na conexão
    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Captura os dados do formulário
    $codigo = $_POST['codigo'];
    $qtde_venda = $_POST['qtde_venda'];
    $desconto = $_POST['desconto'];
    $data_venda = date("Y-m-d");

    // Consulta para obter o preço e a quantidade do produto
    $sql_produto = "SELECT preco, quantidade FROM produtos WHERE codigo = ?";
    $stmt_produto = $conn->prepare($sql_produto);
    $stmt_produto->bind_param('s', $codigo);
    $stmt_produto->execute();
    $result_produto = $stmt_produto->get_result();

    if ($result_produto->num_rows > 0) {
        $produto = $result_produto->fetch_assoc();
        $preco = $produto['preco'];
        $quantidade_estoque = $produto['quantidade'];

        // Verifica se há estoque suficiente
        if ($quantidade_estoque >= $qtde_venda) {
            // Calcula o valor da venda com o desconto
            $valor_total = ($preco * $qtde_venda) * ((100 - $desconto) / 100);

            // Insere a venda no banco de dados
            $sql_venda = "INSERT INTO vendas (codigo, qtde_venda, data_venda, valor, desconto) VALUES (?, ?, ?, ?, ?)";
            $stmt_venda = $conn->prepare($sql_venda);
            $stmt_venda->bind_param('sissd', $codigo, $qtde_venda, $data_venda, $valor_total, $desconto);
            $stmt_venda->execute();

            // Atualiza a quantidade de estoque
            $nova_quantidade = $quantidade_estoque - $qtde_venda;
            $sql_atualiza_estoque = "UPDATE produtos SET quantidade = ? WHERE codigo = ?";
            $stmt_atualiza_estoque = $conn->prepare($sql_atualiza_estoque);
            $stmt_atualiza_estoque->bind_param('is', $nova_quantidade, $codigo);
            $stmt_atualiza_estoque->execute();

            $mensagem = "Venda registrada com sucesso!";
        } else {
            $mensagem = "Não há estoque suficiente para a venda!";
        }
    } else {
        $mensagem = "Produto não encontrado!";
    }

    // Fecha as conexões
    $stmt_produto->close();
    $stmt_venda->close();
    $stmt_atualiza_estoque->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Venda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 50px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: # #0056b3;
        }

        a {
            text-decoration: none;
            display: block;
            text-align: center;
        }

        /* Estilo para a mensagem de sucesso ou erro */
        #mensagem {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #007BFF; /* Azul */
            color: white;
            padding: 20px;
            border-radius: 5px;
            font-size: 18px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
        }

        #mensagem.error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <!-- Mensagem de sucesso ou erro -->
    <?php if ($mensagem): ?>
        <div id="mensagem" class="<?php echo (strpos($mensagem, 'sucesso') !== false) ? '' : 'error'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Cadastrar Venda</h1>
        <form action="vendas.php" method="POST">
            <label for="codigo">Código do Produto:</label>
            <input type="text" id="codigo" name="codigo" required>

            <label for="qtde_venda">Quantidade:</label>
            <input type="number" id="qtde_venda" name="qtde_venda" required>

            <label for="desconto">Desconto (%):</label>
            <input type="number" id="desconto" name="desconto" value="0" required>

            <button type="submit">Cadastrar Venda</button>
        </form>

        <br>
        <!-- Botão de voltar ao dashboard -->
        <a href="dashboard.php">
            <button type="button">Voltar ao Menu Principal</button>
        </a>
    </div>

    <script>
        // Exibe a mensagem e depois a esconde
        window.onload = function() {
            var mensagem = document.getElementById('mensagem');
            if (mensagem) {
                mensagem.style.display = 'block'; // Exibe a mensagem
                setTimeout(function() {
                    mensagem.style.display = 'none'; // Esconde a mensagem após 3 segundos
                }, 3000);
            }
        };
    </script>
</body>
</html>
