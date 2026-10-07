<?php
session_start();

// Proteção da Área Administrativa
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once "../conexao/conexao.php";

$mensagem = "";
$erro = "";

// Processamento do formulário via POST
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

    // Validações
    if (empty($nome) || $categoria_id <= 0 || $preco <= 0) {
        $erro = "Preencha todos os campos obrigatórios!";
    } else {
        $sql = "INSERT INTO produtos (nome, categoria_id, preco, quantidade_estoque, editora_fabricante, especificacao, condicao, descricao, imagem_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sidisssss", $nome, $categoria_id, $preco, $quantidade_estoque, $editora_fabricante, $especificacao, $condicao, $descricao, $imagem_url);

        if (mysqli_stmt_execute($stmt)) {
            $mensagem = "Produto cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar produto: " . mysqli_error($conexao);
        }
    }
}

// Busca as categorias cadastradas para o SELECT
$query_cat = mysqli_query($conexao, "SELECT * FROM categorias ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto - Geek House Admin</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <div class="formulario-card">
        <h1>Cadastrar Item Geek</h1>

        <?php if (!empty($mensagem)): ?>
            <p class="sucesso"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="campo">
                <label>Nome do Produto *</label>
                <input type="text" name="nome" placeholder="Ex: Mangá Chainsaw Man Vol. 1 / Figure Zoro / Card Charizard" required>
            </div>

            <div class="campo">
                <label>Categoria *</label>
                <select name="categoria_id" required>
                    <option value="">Selecione...</option>
                    <?php while ($cat = mysqli_fetch_assoc($query_cat)): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo-grupo">
                <div class="campo">
                    <label>Preço (R$) *</label>
                    <input type="number" step="0.01" name="preco" placeholder="0.00" required>
                </div>

                <div class="campo">
                    <label>Qtd. Estoque *</label>
                    <input type="number" name="quantidade_estoque" value="1" min="0" required>
                </div>
            </div>

            <div class="campo">
                <label>Editora / Fabricante / Marca</label>
                <input type="text" name="editora_fabricante" placeholder="Ex: Panini, Bandai, Copag">
            </div>

            <div class="campo">
                <label>Especificação (Volume / Escala / Raridade)</label>
                <input type="text" name="especificacao" placeholder="Ex: Vol. 12 / Escala 1/8 / Rara Holofoil">
            </div>

            <div class="campo">
                <label>Condição</label>
                <select name="condicao">
                    <option value="Novo/Lacrado">Novo / Lacrado</option>
                    <option value="Seminovo">Seminovo</option>
                    <option value="Raro/Colecionável">Raro / Colecionável</option>
                </select>
            </div>

            <div class="campo">
                <label>URL da Imagem</label>
                <input type="url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg">
            </div>

            <div class="campo">
                <label>Descrição</label>
                <textarea name="descricao" rows="3" placeholder="Detalhes do item..."></textarea>
            </div>

            <button type="submit" class="btn-enviar">Salvar Produto</button>
            <a href="produtos_listar.php" class="btn-voltar">Voltar para Lista</a>
        </form>
    </div>
</body>
</html>
