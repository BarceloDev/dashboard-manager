CREATE DATABASE IF NOT EXISTS enterprise CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enterpriseregister (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  senha varchar(100) NOT NULL,
  telefone varchar(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS employeeregister (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  senha varchar(100) NOT NULL,
  telefone varchar(20) NOT NULL,
  enterpriseName varchar(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS notesregister (
  id int(11) NOT NULL,
  vendedor varchar(100) NOT NULL,
  cliente varchar(100) NOT NULL,
  produto varchar(100) NOT NULL,
  price decimal(6,2) NOT NULL,
  enterpriseName varchar(100) DEFAULT NULL
)

INSERT INTO notesregister (vendedor, cliente, produto, price, enterpriseName) VALUES
('Funcionário1', 'Cliente1', 'Produto-A', 210.89, 'Dash Enterprise'),
('Funcionário1', 'Cliente2', 'Produto-B', 123.76, 'Dash Enterprise'),
('Funcionário1', 'Cliente3', 'Produt-C', 541.90, 'Dash Enterprise'),
('Funcionário2', 'Cliente4', 'Produto-D', 431.80, 'Dash Enterprise'),
('Funcionário2', 'Cliente5', 'Produto-E', 43.80, 'Dash Enterprise'),
('Funcionário2', 'Cliente6', 'Produto-F', 541.90, 'Dash Enterprise');

INSERT INTO enterpriseregister (nome, email, senha, telefone) VALUES
('Dash Enterprise', 'dash@gmail.com', 123, '1234567890'),
('DashManager', 'dashmanager@gmail.com', 123, '1234567890');


INSERT INTO employeeregister (nome, email, senha, telefone, enterpriseName) VALUES
('Funcionário1', 'funcionario1@gmail.com', 123, '1234567890', 'Dash Enterprise'),
('Funcionário2', 'funcionario2@gmail.com', 123, '1234567890', 'Dash Enterprise'),
('Funcionário4', 'funcionario4@gmail.com', 123, '1234567890', 'DashManager'),
('Funcionário5', 'funcionario5@gmail.com', 123, '1234567890', 'DashManager'),
('Funcionário6', 'funcionario6@gmail.com', 123, '1234567890', 'DashManager'),
('Funcionário8', 'funcionario8@gmail.com', 123, '1234567890', 'Dash Enterprise');
