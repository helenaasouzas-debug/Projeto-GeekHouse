<?php
require_once "conexao/conexao.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Ações do Carrinho (Adicionar, Remover, Limpar)
$acao = $_GET['acao'] ?? '';
$id   = (int) ($_GET['id'] ?? 0);

if ($acao === 'add' && $id > 0) {
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]++;
    } else {
        $_SESSION['carrinho'][$id] = 1;
    }
    header("Location: carrinho.php");
    exit;
}

if ($acao === 'remove' && $id > 0) {
    unset($_SESSION['carrinho'][$id]);
    header("Location: carrinho.php");
    exit;
}

if ($acao === 'limpar') {
    $_SESSION['carrinho'] = [];
    header("Location: carrinho.php");
    exit;
}

include "includes/header.php";
?>

<h1 class="titulo-pagina">Seu Carrinho de Compras</h1>

<?php if (empty($_SESSION['carrinho'])): ?>
    <div class="card-box">
        <p>Seu carrinho está vazio!</p>
        <a href="produtos.php" class="btn-primary">Ir para a Loja</a>
    </div>
<?php else: ?>
    <div class="tabela-container">
        <table class="tabela-admin" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #8e245f; color: white;">
                    <th style="padding:10px;">Item</th>
                    <th style="padding:10px;">Preço Un.</th>
                    <th style="padding:10px;">Qtd.</th>
                    <th style="padding:10px;">Subtotal</th>
                    <th style="padding:10px;">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_geral = 0;
                $ids = implode(',', array_keys($_SESSION['carrinho']));
                $sql = "SELECT * FROM produtos WHERE id IN ($ids)";
                $resultado = mysqli_query($conexao, $sql);

                while ($prod = mysqli_fetch_assoc($resultado)):
                    $qtd = $_SESSION['carrinho'][$prod['id']];
                    $subtotal = $prod['preco'] * $qtd;
                    $total_geral += $subtotal;
                ?>
                    <tr style="border-bottom: 1px solid #7c226a; color: white;">
                        <td style="padding:10px;"><?= htmlspecialchars($prod['nome']) ?></td>
                        <td style="padding:10px;">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                        <td style="padding:10px;"><?= $qtd ?></td>
                        <td style="padding:10px;">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                        <td style="padding:10px;">
                            <a href="carrinho.php?acao=remove&id=<?= $prod['id'] ?>" style="color:#ff9999; font-weight:bold;">[Remover]</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-carrinho-box">
            <h3>Total do Pedido: <span style="color:#66ff99;">R$ <?= number_format($total_geral, 2, ',', '.') ?></span></h3>
            
            <a href="carrinho.php?acao=limpar" class="btn-voltar" style="margin-right:15px;">Limpar Carrinho</a>
            <a href="finalizar.php" class="btn-enviar" style="display:inline-block; width:auto; padding:12px 25px;">Finalizar Compra →</a>
        </div>
    </div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
