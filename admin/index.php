<?php
require_once "../conexao/auth.php";
verificar_admin(); // Trava de segurança: redireciona se não for admin
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Geek House</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="formulario-card">
        <h1>Painel Administrativo</h1>
        <p>Bem-vindo, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>!</p>
        
        <ul>
            <li><a href="produto_cadastrar.php" style="color: #f0d5ff;">Cadastrar Novo Item Geek</a></li>
            <li><a href="produtos_listar.php" style="color: #f0d5ff;">Gerenciar Produtos (Editar / Excluir)</a></li>
        </ul>

        <a href="../perfil.php" class="btn-voltar">Ver Perfil</a> | 
        <a href="../logout.php" class="btn-voltar">Sair</a>
    </div>
</body>
</html>
