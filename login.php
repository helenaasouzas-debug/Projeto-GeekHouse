<?php
session_start();

// Se o usuário já estiver logado, redireciona para a página apropriada
if (isset($_SESSION['usuario'])) {
    if ($_SESSION['usuario']['tipo'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: perfil.php");
    }
    exit;
}

require_once "conexao/conexao.php";

$erro = "";

// Verifica se a requisição veio do formulário via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro = "Preencha o e-mail e a senha!";
    } else {
        // Consulta o usuário no banco de dados com Prepared Statement para segurança
        $sql  = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($usuario = mysqli_fetch_assoc($resultado)) {
            // Verifica a senha (pode usar password_verify para senhas com hash)
            if ($senha === $usuario['senha']) {
                // Cria a sessão do usuário autenticado
                $_SESSION['usuario'] = [
                    'id'    => $usuario['id'],
                    'nome'  => $usuario['nome'],
                    'email' => $usuario['email'],
                    'tipo'  => $usuario['tipo']
                ];

                // Redireciona conforme o perfil
                if ($usuario['tipo'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: perfil.php");
                }
                exit;
            } else {
                $erro = "E-mail ou senha incorretos!";
            }
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Geek House</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="formulario-card">
        <h1>Geek House - Login</h1>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['erro']) && $_GET['erro'] === 'acesso_negado'): ?>
            <p class="erro">Você precisa estar logado para acessar essa área!</p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="btn-enviar">Entrar</button>
            <a href="index.php" class="btn-voltar">Voltar para a Loja</a>
        </form>
    </div>
</body>
</html>
