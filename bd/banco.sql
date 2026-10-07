-- Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS loja_geek;
USE loja_geek;

-- Tabela de Usuários (obrigatória para Login e Sessão)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin', 'cliente') DEFAULT 'cliente'
);

-- Tabela 1 do Negócio: Categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

-- Inserindo categorias iniciais do seu projeto
INSERT INTO categorias (nome) VALUES ('Mangás'), ('Figures'), ('Cartas Pokémon');

-- Tabela 2 do Negócio: Produtos (com campos específicos geek)
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    categoria_id INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade_estoque INT NOT NULL DEFAULT 0,
    editora_fabricante VARCHAR(100), -- Ex: Panini (Mangá), Bandai (Figure), Copag (Pokémon)
    especificacao VARCHAR(150),      -- Ex: Volume/Autor, Escala/Altura, Raridade/Coleção
    condicao ENUM('Novo/Lacrado', 'Seminovo', 'Raro/Colecionável') DEFAULT 'Novo/Lacrado',
    descricao TEXT,
    imagem_url VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Tabela 3 do Negócio: Vendas / Pedidos (para a operação de compra)
CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    valor_total DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(50) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela 4 do Negócio: Itens da Venda
CREATE TABLE itens_venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);
