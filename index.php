<?php
require_once "conexao/conexao.php";
include "includes/header.php";

// Busca os 6 produtos mais recentes do banco
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.id DESC LIMIT 6";
$resultado = mysqli_query($conexao, $sql);
?>

<div class="banner-home">
    <h1>Bem-vindo à Geek House!</h1>
    <p>O seu universo geek em um só lugar. Mangás, Action Figures e Cartas Pokémon colecionáveis!</p>
    <a href="produtos.php" class="btn-enviar" style="display:inline-block; width:auto; padding: 12px 25px;">Ver Todo o Catálogo</a>
</div>

<h2 class="titulo-secao">Destaques da Loja</h2>

<div class="grid-produtos">
    <?php if (mysqli_num_rows($resultado) > 0): ?>
        <?php while ($prod = mysqli_fetch_assoc($resultado)): ?>
            <div class="card-produto">
                <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'https://via.placeholder.com/300x300?text=Geek+House') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <div class="card-body">
                    <span class="categoria-tag"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                    <h3><?= htmlspecialchars($prod['nome']) ?></h3>
                    <p class="especificacao"><?= htmlspecialchars($prod['editora_fabricante'] ?? '') ?> - <?= htmlspecialchars($prod['especificacao'] ?? '') ?></p>
                    <p class="preco">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
                    <div class="card-acoes">
                        <a href="detalhe.php?id=<?= $prod['id'] ?>" class="btn-detalhes">Ver Detalhes</a>
                        <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-comprar">+ Carrinho</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum produto cadastrado no momento.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
