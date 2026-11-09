-- GameStore - schema + carga inicial
CREATE DATABASE IF NOT EXISTS gamestore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamestore;

DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL UNIQUE,
  senha_hash  VARCHAR(255) NOT NULL,              -- password_hash()
  perfil      ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
  criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE produtos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id  INT UNSIGNED NOT NULL,
  nome          VARCHAR(150) NOT NULL,
  descricao     TEXT,
  preco         DECIMAL(10,2) NOT NULL CHECK (preco >= 0),
  estoque       INT UNSIGNED NOT NULL DEFAULT 0,
  imagem        VARCHAR(255),
  ativo         TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_prod_cat FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id       INT UNSIGNED NOT NULL,
  data_pedido      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  forma_pagamento  ENUM('PIX','Boleto','Cartao') NOT NULL,
  total            DECIMAL(10,2) NOT NULL,
  status           VARCHAR(30) NOT NULL DEFAULT 'Pago',
  CONSTRAINT fk_ped_user FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE itens_pedido (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id       INT UNSIGNED NOT NULL,
  produto_id      INT UNSIGNED NOT NULL,
  quantidade      INT UNSIGNED NOT NULL,
  preco_unitario  DECIMAL(10,2) NOT NULL,         -- preço congelado na compra
  CONSTRAINT fk_item_ped  FOREIGN KEY (pedido_id)  REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_item_prod FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

-- ===== Carga inicial =====
-- admin@loja.com / admin123  |  cliente@loja.com / cliente123
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES
('Administrador', 'admin@loja.com',   '$2y$10$QLdCojlcyijJTHkTpKeQJOexS8u0bnWg5lXEWz6DrG1Uuffx1t45.', 'admin'),
('Cliente Teste', 'cliente@loja.com', '$2y$10$0AC/W9KXjrhMPmvYsFTUu.KlxnqARsU8luWL4vjv7iRRg9qGP0MVu', 'cliente');

INSERT INTO categorias (nome) VALUES ('Consoles'), ('Jogos'), ('Acessórios'), ('PC Gamer');

INSERT INTO produtos (categoria_id, nome, descricao, preco, estoque, imagem) VALUES
(1, 'Console NextGen 1TB',      'Console de nova geração com SSD ultrarrápido e 4K a 120 fps.', 3999.90, 10, 'https://placehold.co/600x400?text=Console'),
(1, 'Console Portátil Pro',     'Jogue em qualquer lugar com tela OLED de 7 polegadas.',        2899.00,  8, 'https://placehold.co/600x400?text=Portatil'),
(2, 'Aventura Épica',           'RPG de mundo aberto com mais de 100 horas de história.',        249.90, 25, 'https://placehold.co/600x400?text=Aventura'),
(2, 'Corrida Extrema',          'Simulador de corrida com gráficos realistas.',                  199.90, 30, 'https://placehold.co/600x400?text=Corrida'),
(2, 'Futebol Manager 2026',     'Comande seu time rumo ao título mundial.',                      179.90,  0, 'https://placehold.co/600x400?text=Futebol'),
(3, 'Controle Sem Fio',         'Controle ergonômico com vibração háptica.',                     349.90, 40, 'https://placehold.co/600x400?text=Controle'),
(3, 'Headset 7.1',              'Som surround 7.1 com microfone removível.',                     399.00, 15, 'https://placehold.co/600x400?text=Headset'),
(4, 'Teclado Mecânico RGB',     'Switches azuis, iluminação RGB por tecla.',                     289.00, 20, 'https://placehold.co/600x400?text=Teclado'),
(4, 'Mouse Gamer 16000 DPI',    'Sensor óptico de alta precisão e 8 botões.',                    159.90, 35, 'https://placehold.co/600x400?text=Mouse'),
(4, 'Monitor 27" 165Hz',        'Painel IPS, 1ms, ideal para jogos competitivos.',              1599.00,  6, 'https://placehold.co/600x400?text=Monitor');
