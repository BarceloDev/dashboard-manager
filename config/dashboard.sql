CREATE DATABASE IF NOT EXISTS enterprise CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enterpriseRegister (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS employeeRegister (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    enterpriseName VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS notesRegister (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    vendedor VARCHAR(100) NOT NULL,
    cliente VARCHAR(100) NOT NULL,
    produto VARCHAR(100) NOT NULL,
    price DECIMAL(6, 2) NOT NULL
);

INSERT INTO notesRegister (vendedor, cliente, produto, price) VALUES
('Alice', 'Cliente1', 'Produto X', 150.00),
('Bob', 'Cliente2', 'Produto Y', 200.50),
('Charlie', 'Cliente3', 'Produto Z', 300.75);

INSERT INTO employeeRegister (nome, email, senha, telefone, enterpriseName) VALUES 
('Eduardo', 'eduardo@empresa.com', 'senha123', '1234567890', 'Teste1'),
('Fernanda', 'fernanda@empresa.com', 'senha456', '0987654321', 'Teste1'),
('Gabriel', 'gabriel@empresa.com', 'senha789', '1122334455', 'Teste1'),
('Heloisa', 'heloisa@empresa.com', 'senha000', '5555555555', 'Teste2');
