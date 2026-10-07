<?php
require_once "conexao/conexao.php";

$id = (int) ($_GET['id'] ?? 0);

$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.id = ? LIMIT 1";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$produto = mysqli_fetch_assoc($resultado);

if (!$produto) {
    header("Location: produtos.php");
    exit;
}

include "includes/header.php";
?>

<div class="card-detalhe-produto">
    <div class="detalhe-imagem">
        <img src="<?= htmlspecialchars($produto['imagem_url'] ?: 'https://via.placeholder.com/400x400?text=Geek+House') ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
    </div>
    <div class="detalhe-info">
        <span class="categoria-tag"><?= htmlspecialchars($produto['categoria_nome']) ?></span>
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        
        <p class="preco-detalhe">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
        
        <ul class="lista-especificacoes">
            <li><strong>Editora / Fabricante:</strong> <?= htmlspecialchars($produto['editora_fabricante'] ?: 'Não informado') ?></li>
            <li><strong>Especificação:</strong> <?= htmlspecialchars($produto['especificacao'] ?: 'N/A') ?></li>
            <li><strong>Condição:</strong> <?= htmlspecialchars($produto['condicao']) ?></li>
            <li><strong>Estoque Disponível:</strong> <?= $produto['quantidade_estoque'] ?> unidades</li>
        </ul>

        <p class="descricao"><?= nl2br(htmlspecialchars($produto['descricao'] ?: 'Sem descrição informada.')) ?></p>

        <?php if ($produto['quantidade_estoque'] > 0): ?>
            <a href="carrinho.php?acao=add&id=<?= $produto['id'] ?>" class="btn-enviar" style="display:inline-block; text-align:center; text-decoration:none;">Adicionar ao Carrinho</a>
        <?php else: ?>
            <p class="erro">Produto Indisponível no Momento</p>
        <?php endif; ?>
        
        <br><br>
        <a href="produtos.php" class="btn-voltar">← Voltar para o Catálogo</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>
