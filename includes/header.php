<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calcula a quantidade total de itens no carrinho
$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $qtd) {
        $total_itens_carrinho += $qtd;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geek House - Seu Universo Geek</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header class="top-header">
        <div class="logo">
            <a href="index.php" style="color: white; text-decoration: none;">👾 Geek House</a>
        </div>
        <nav class="nav-menu">
            <a href="index.php">Início</a>
            <a href="produtos.php">Produtos</a>
            <a href="sobre.php">Sobre Nós</a>
            <a href="carrinho.php" class="btn-carrinho">🛒 Carrinho (<?= $total_itens_carrinho ?>)</a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="perfil.php">Meu Perfil</a>
                <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <a href="admin/index.php" class="badge-admin">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn-login-menu">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container-principal">
