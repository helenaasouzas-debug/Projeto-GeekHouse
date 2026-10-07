<?php
// Garante que a sessão esteja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o usuário está logado no sistema.
 * Se não estiver, redireciona para a tela de login.
 */
function verificar_login() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../login.php?erro=acesso_negado");
        exit;
    }
}

/**
 * Verifica se o usuário logado possui perfil de Administrador.
 * Impede que clientes comuns acessem páginas administrativas.
 */
function verificar_admin() {
    verificar_login(); // Primeiro confirma se está logado
    
    if ($_SESSION['usuario']['tipo'] !== 'admin') {
        header("Location: ../index.php?erro=sem_permissao");
        exit;
    }
}
?>
