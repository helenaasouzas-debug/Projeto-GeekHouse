<?php
require_once "conexao/auth.php";
verificar_login(); // Exige estar logado para fechar o pedido

require_once "conexao/conexao.php";

if (empty($_SESSION['carrinho'])) {
    header("Location: produtos.php");
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];
$mensagem_sucesso = "";
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forma_pagamento = $_POST['forma_pagamento'] ?? 'Pix';

    // 1. Calcula o total da venda consultando os preços no banco
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    $sql_prods = "SELECT id, preco, quantidade_estoque FROM produtos WHERE id IN ($ids)";
    $res_prods = mysqli_query($conexao, $sql_prods);

    $valor_total = 0;
    $itens_compra = [];

    while ($p = mysqli_fetch_assoc($res_prods)) {
        $qtd_pedida = $_SESSION['carrinho'][$p['id']];
        $valor_total += ($p['preco'] * $qtd_pedida);
        $itens_compra[] = [
            'produto_id' => $p['id'],
            'quantidade' => $qtd_pedida,
            'preco_unitario' => $p['preco']
        ];
    }

    // 2. Insere na tabela de Vendas
    $sql_venda = "INSERT INTO vendas (usuario_id, valor_total, forma_pagamento) VALUES (?, ?, ?)";
    $stmt_venda = mysqli_prepare($conexao, $sql_venda);
    mysqli_stmt_bind_param($stmt_venda, "ids", $usuario_id, $valor_total, $forma_pagamento);

    if (mysqli_stmt_execute($stmt_venda)) {
        $venda_id = mysqli_insert_id($conexao);

        // 3. Insere os Itens da Venda e atualiza o Estoque
        foreach ($itens_compra as $item) {
            $sql_item = "INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($conexao, $sql_item);
            mysqli_stmt_bind_param($stmt_item, "iiid", $venda_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario']);
            mysqli_stmt_execute($stmt_item);

            // Baixa no estoque
            $sql_estoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id = ?";
            $stmt_est = mysqli_prepare($conexao, $sql_estoque);
            mysqli_stmt_bind_param($stmt_est, "ii", $item['quantidade'], $item['produto_id']);
            mysqli_stmt_execute($stmt_est);
        }

        // Limpa o carrinho
        $_SESSION['carrinho'] = [];
        $mensagem_sucesso = "Pedido #$venda_id finalizado com sucesso! Obrigado por comprar na Geek House!";
    } else {
        $erro = "Erro ao processar o pedido: " . mysqli_error($conexao);
    }
}

include "includes/header.php";
?>

<div class="card-box" style="max-width: 600px;">
    <?php if (!empty($mensagem_sucesso)): ?>
        <h2 style="color: #66ff99;">Pedido Confirmado! 🎉</h2>
        <p><?= $mensagem_sucesso ?></p>
        <br>
        <a href="produtos.php" class="btn-primary">Voltar às Compras</a>
    <?php else: ?>
        <h2>Finalizar Pedido</h2>
        <p>Cliente: <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong></p>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="finalizar.php">
            <div class="campo" style="text-align: left;">
                <label>Forma de Pagamento:</label>
                <select name="forma_pagamento" required>
                    <option value="Pix">Pix (Aprovação Instantânea)</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Boleto">Boleto Bancário</option>
                </select>
            </div>

            <button type="submit" class="btn-enviar">Confirmar e Pagar</button>
        </form>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
