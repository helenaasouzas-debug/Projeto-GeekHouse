<?php
require_once "conexao/conexao.php";
include "includes/header.php";

$categoria_filtro = (int) ($_GET['categoria'] ?? 0);
$busca            = trim($_GET['busca'] ?? '');

// Monta a consulta dinâmica com filtros
$sql = "SELECT p.*, c.nome AS categoria_nome 
        FROM produtos p 
        INNER JOIN categorias c ON p.categoria_id = c.id 
        WHERE 1=1";

if ($categoria_filtro > 0) {
    $sql .= " AND p.categoria_id = " . $categoria_filtro;
}

if (!empty($busca)) {
    $busca_escapada = mysqli_real_escape_string($conexao, $busca);
    $sql .= " AND p.nome LIKE '%$busca_escapada%'";
}

$sql .= " ORDER BY p.nome ASC";
$resultado = mysqli_query($conexao, $sql);

// Busca categorias para preencher o select de filtros
$categorias_query = mysqli_query($conexao, "SELECT * FROM categorias ORDER BY nome ASC");
?>

<h1 class="titulo-pagina">Catálogo Geek</h1>

<!-- Formulário de Filtro e Busca -->
<div class="card-filtro">
    <form method="GET" action="produtos.php" class="form-busca-inline">
        <input type="text" name="busca" placeholder="Buscar mangá, figure, pokémon..." value="<?= htmlspecialchars($busca) ?>">
        <select name="categoria">
            <option value="0">Todas as Categorias</option>
            <?php while ($cat = mysqli_fetch_assoc($categorias_query)): ?>
                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categoria_filtro) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nome']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn-enviar" style="margin:0; width:auto;">Filtrar</button>
    </form>
</div>

<div class="grid-produtos">
    <?php if (mysqli_num_rows($resultado) > 0): ?>
        <?php while ($prod = mysqli_fetch_assoc($resultado)): ?>
            <div class="card-produto">
                <img src="<?= htmlspecialchars($prod['imagem_url'] ?: 'https://via.placeholder.com/300x300?text=Geek+House') ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                <div class="card-body">
                    <span class="categoria-tag"><?= htmlspecialchars($prod['categoria_nome']) ?></span>
                    <h3><?= htmlspecialchars($prod['nome']) ?></h3>
                    <p class="especificacao"><?= htmlspecialchars($prod['especificacao'] ?? '') ?></p>
                    <p class="preco">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
                    <div class="card-acoes">
                        <a href="detalhe.php?id=<?= $prod['id'] ?>" class="btn-detalhes">Ver Detalhes</a>
                        <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn-comprar">+ Carrinho</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="mensagem-alerta">Nenhum produto encontrado com os filtros selecionados.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
