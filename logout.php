<?php
// Inicia a sessão
session_start();

// Destroi todas as variáveis de sessão
session_unset();

// Destroi a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.html"); // ou para o dashboard se preferir
exit();
?>
