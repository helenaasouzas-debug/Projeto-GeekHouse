<?php
require_once "conexao/auth.php";
verificar_login(); // Bloqueia acesso não autenticado

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil - Geek House</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="formulario-card">
        <h1>Meu Perfil</h1>
        <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
        <p><strong>Tipo de Conta:</strong> <?= strtoupper(htmlspecialchars($usuario['tipo'])) ?></p>

        <hr>

        <?php if ($usuario['tipo'] === 'admin'): ?>
            <a href="admin/index.php" class="btn-enviar" style="display:block; text-align:center; text-decoration:none;">Painel Admin</a>
        <?php endif; ?>

        <a href="logout.php" class="btn-voltar">Sair / Logout</a>
    </div>
</body>
</html>
