<?php
require_once "../conexao/auth.php";
verificar_admin(); // Trava de segurança: restrito a administradores

require_once "../conexao/conexao.php";

// Consulta SQL com JOIN para trazer o nome da categoria junto com o produto
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC";
$resultado = mysqli_query($conexao, $sql);

// Captura mensagens de feedback via GET
$mensagem = $_GET['mensagem'] ?? '';
$erro     = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos - Geek House Admin</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="formulario-card" style="max-width: 900px;">
        <h1>Gerenciar Produtos Geek</h1>

        <?php if (!empty($mensagem)): ?>
            <p class="sucesso"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <div style="margin-bottom: 20px; text-align: right;">
            <a href="produto_cadastrar.php" class="btn-enviar" style="text-decoration:none; padding: 10px 15px;">+ Cadastrar Novo Item</a>
        </div>

        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table class="tabela-admin" style="width:100%; border-collapse: collapse; margin-top: 15px; color: white;">
                <thead>
                    <tr style="background-color: #8e245f; text-align: left;">
                        <th style="padding: 10px;">ID</th>
                        <th style="padding: 10px;">Produto</th>
                        <th style="padding: 10px;">Categoria</th>
                        <th style="padding: 10px;">Preço</th>
                        <th style="padding: 10px;">Estoque</th>
                        <th style="padding: 10px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prod = mysqli_fetch_assoc($resultado)): ?>
                        <tr style="border-bottom: 1px solid #7c226a;">
                            <td style="padding: 10px;"><?= $prod['id'] ?></td>
                            <td style="padding: 10px;">
                                <strong><?= htmlspecialchars($prod['nome']) ?></strong><br>
                                <small style="color: #d8b4e8;"><?= htmlspecialchars($prod['editora_fabricante'] ?? '') ?></small>
                            </td>
                            <td style="padding: 10px;"><?= htmlspecialchars($prod['categoria_nome']) ?></td>
                            <td style="padding: 10px;">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                            <td style="padding: 10px;"><?= $prod['quantidade_estoque'] ?> un.</td>
                            <td style="padding: 10px;">
                                <a href="produto_editar.php?id=<?= $prod['id'] ?>" style="color: #f0d5ff; font-weight: bold; margin-right: 10px;">[Editar]</a>
                                <a href="produto_excluir.php?id=<?= $prod['id'] ?>" 
                                   style="color: #ff9999; font-weight: bold;" 
                                   onclick="return confirm('Tem certeza que deseja excluir o item \'<?= htmlspecialchars($prod['nome']) ?>\'?');">[Excluir]</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #f0d5ff;">Nenhum produto cadastrado no momento.</p>
        <?php endif; ?>

        <div style="margin-top: 25px;">
            <a href="index.php" class="btn-voltar">Voltar para o Painel Admin</a>
        </div>
    </div>
</body>
</html>
