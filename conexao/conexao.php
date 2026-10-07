<?php
// Configurações do banco de dados MySQL
$host    = "localhost";
$usuario = "root";
$senha   = "";
$banco   = "loja_geek";

// Criação da conexão com o MySQL
$conexao = mysqli_connect($host, $usuario, $senha, $banco);

// Verifica se houve erro de conexão
if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Define o conjunto de caracteres para evitar problemas de acentuação
mysqli_set_charset($conexao, "utf8mb4");
?>
