<?php
require_once "../conexao/auth.php";
verificar_admin(); // Trava de segurança

require_once "../conexao/conexao.php";

$id = (int) ($_GET['id'] ?? 0);
$erro = "";
$mensagem = "";

// Verifica se o ID foi informado
if ($id <= 0) {
    header("Location: produtos_listar.php?erro=ID+inv%C3%A1lido!");
    exit;
}

// Processa a atualização dos dados recebidos via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome               = trim($_POST['nome'] ?? '');
    $categoria_id       = (int) ($_POST['categoria_id'] ?? 0);
    $preco              = (float) ($_POST['preco'] ?? 0);
    $quantidade_estoque = (int) ($_POST['quantidade_estoque'] ?? 0);
    $editora_fabricante = trim($_POST['editora_fabricante'] ?? '');
    $especificacao      = trim($_POST['especificacao'] ?? '');
    $condicao           = $_POST['condicao'] ?? 'Novo/Lacrado';
    $descricao          = trim($_POST['descricao'] ?? '');
    $imagem_url         = trim($_POST['imagem_url'] ?? '');

    if (empty($nome) || $categoria_id <= 0 || $preco <= 0) {
        $erro = "Preencha todos os campos obrigatórios!";
    } else {
        // Atualização com Prepared Statement para maior segurança
        $sql = "UPDATE produtos 
                SET nome = ?, categoria_id = ?, preco = ?, quantidade_estoque = ?, editora_fabricante = ?, especificacao = ?, condicao = ?, descricao = ?, imagem_url = ?
                WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sidisssssi", $nome, $categoria_id, $preco, $quantidade_estoque, $editora_fabricante, $especificacao, $condicao, $descricao, $imagem_url, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: produtos_listar.php?mensagem=Produto+atualizado+com+sucesso!");
            exit;
        } else {
            $erro = "Erro ao atualizar produto: " . mysqli_error($conexao);
        }
    }
}

// Busca os dados atuais do produto para preencher o formulário
$sql_busca  = "SELECT * FROM produtos WHERE id = ? LIMIT 1";
$stmt_busca = mysqli_prepare($conexao, $sql_busca);
mysqli_stmt_bind_param($stmt_busca, "i", $id);
mysqli_stmt_execute($stmt_busca);
$resultado_busca = mysqli_stmt_get_result($stmt_busca);
$produto = mysqli_fetch_assoc($resultado_busca);

if (!$produto) {
    header("Location: produtos_listar.php?erro=Produto+n%C3%A3o+encontrado!");
    exit;
}

// Busca as categorias cadastradas
$query_cat = mysqli_query($conexao, "SELECT * FROM categorias ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto - Geek House Admin</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="formulario-card">
        <h1>Editar Item Geek #<?= $produto['id'] ?></h1>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="campo">
                <label>Nome do Produto *</label>
                <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
            </div>

            <div class="campo">
                <label>Categoria *</label>
                <select name="categoria_id" required>
                    <option value="">Selecione...</option>
                    <?php while ($cat = mysqli_fetch_assoc($query_cat)): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $produto['categoria_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Preço (R$) *</label>
                    <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" required>
                </div>

                <div class="campo">
                    <label>Qtd. Estoque *</label>
                    <input type="number" name="quantidade_estoque" value="<?= $produto['quantidade_estoque'] ?>" min="0" required>
                </div>
            </div>

            <div class="campo">
                <label>Editora / Fabricante</label>
                <input type="text" name="editora_fabricante" value="<?= htmlspecialchars($produto['editora_fabricante'] ?? '') ?>">
            </div>

            <div class="campo">
                <label>Especificação (Vol. / Escala / Raridade)</label>
                <input type="text" name="especificacao" value="<?= htmlspecialchars($produto['especificacao'] ?? '') ?>">
            </div>

            <div class="campo">
                <label>Condição</label>
                <select name="condicao">
                    <option value="Novo/Lacrado" <?= ($produto['condicao'] === 'Novo/Lacrado') ? 'selected' : '' ?>>Novo / Lacrado</option>
                    <option value="Seminovo" <?= ($produto['condicao'] === 'Seminovo') ? 'selected' : '' ?>>Seminovo</option>
                    <option value="Raro/Colecionável" <?= ($produto['condicao'] === 'Raro/Colecionável') ? 'selected' : '' ?>>Raro / Colecionável</option>
                </select>
            </div>

            <div class="campo">
                <label>URL da Imagem</label>
                <input type="url" name="imagem_url" value="<?= htmlspecialchars($produto['imagem_url'] ?? '') ?>">
            </div>

            <div class="campo">
                <label>Descrição</label>
                <textarea name="descricao" rows="3"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-enviar">Atualizar Produto</button>
            <a href="produtos_listar.php" class="btn-voltar">Cancelar e Voltar</a>
        </form>
    </div>
</body>
</html>
