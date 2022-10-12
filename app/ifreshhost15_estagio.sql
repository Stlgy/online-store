-- Adminer 4.8.1 MySQL 5.5.5-10.3.36-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `exportador_clientes`;
CREATE TABLE `exportador_clientes` (
  `id` bigint(200) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `chave` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `exportador_clientes` (`id`, `nome`, `chave`) VALUES
(1,	'DEMO',	'ec7289f9e6b744f94869aef4a4ad411921cd55e18f43b1fc1f3e03e65f001a0');

DROP TABLE IF EXISTS `exportador_contactos`;
CREATE TABLE `exportador_contactos` (
  `id_contacto` bigint(200) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `assunto` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id_contacto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `exportador_contactos` (`id_contacto`, `nome`, `email`, `assunto`, `mensagem`) VALUES
(1,	'teste',	'teste@gmail.com',	'testing',	'cdcdcd'),
(2,	'teste',	'teste@gmail.com',	'testing',	'cdcdcd'),
(28,	'ines',	'ines@ideiasfrescas.com',	'testing',	'abc'),
(32,	'teste',	'ines@ideiasfrescas.com',	'testing',	'gfbgf'),
(33,	'teste',	'ines@ideiasfrescas.com',	'testing',	'gfbgf'),
(34,	'teste',	'ines@ideiasfrescas.com',	'testing',	'bgrb');

DROP TABLE IF EXISTS `exportador_produtos`;
CREATE TABLE `exportador_produtos` (
  `id_produto` bigint(200) NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint(200) NOT NULL,
  `referencia` varchar(255) NOT NULL,
  `codigo_barras` varchar(30) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `link_imagem` varchar(255) NOT NULL,
  `disponibilidade` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `preco_promocao` decimal(10,2) NOT NULL,
  `marca` varchar(255) NOT NULL,
  `marca_vendedor` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `stock` int(32) NOT NULL,
  `preco_portes_normal` decimal(10,2) NOT NULL,
  `prazo_entrega_min` int(32) NOT NULL,
  `prazo_entrega_max` int(32) NOT NULL,
  `prazo_preparacao_min` int(32) NOT NULL,
  `prazo_preparacao_max` int(32) NOT NULL,
  `tamanho` varchar(25) NOT NULL,
  `condicao` varchar(50) NOT NULL,
  `cor` varchar(50) NOT NULL,
  `faixa_etaria` varchar(50) NOT NULL,
  `genero` varchar(10) NOT NULL,
  PRIMARY KEY (`id_produto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `exportador_produtos` (`id_produto`, `id_cliente`, `referencia`, `codigo_barras`, `nome`, `descricao`, `link`, `link_imagem`, `disponibilidade`, `preco`, `preco_promocao`, `marca`, `marca_vendedor`, `categoria`, `stock`, `preco_portes_normal`, `prazo_entrega_min`, `prazo_entrega_max`, `prazo_preparacao_min`, `prazo_preparacao_max`, `tamanho`, `condicao`, `cor`, `faixa_etaria`, `genero`) VALUES
(1,	1,	'LCO006whli',	'10',	'Creatures Comp 6&#39; lime',	'descrição do produto',	'https://rochasurfshop.com/product/swimwear-boys/rip-curl-oceanz-volley/6704',	'https://rochasurfshop.com/downloads/produtos/6704/imagem.jpg',	'in_stock',	32.00,	0.00,	'Creatures Comp',	'3065',	'{\"fb\":1,\"gg\":\"23\",\"kk\":\"Calções de Banho\"}',	50,	0.00,	0,	0,	0,	0,	'L',	'new',	'preto',	'kids',	''),
(2,	1,	'LCO006whli',	'10',	'Creatures Comp 6&#39; lime',	'descrição do produto',	'https://rochasurfshop.com/product/swimwear-boys/rip-curl-oceanz-volley/6704',	'https://rochasurfshop.com/downloads/produtos/6704/imagem.jpg',	'in_stock',	32.00,	0.00,	'Creatures Comp',	'3065',	'{\"fb\":1,\"gg\":\"23\",\"kk\":\"Calções de Banho\"}',	50,	0.00,	0,	0,	0,	0,	'L',	'new',	'preto',	'kids',	''),
(3,	1,	'LCO006whli',	'10',	'Creatures Comp 6&#39; lime',	'descrição do produto',	'https://rochasurfshop.com/product/swimwear-boys/rip-curl-oceanz-volley/6704',	'https://rochasurfshop.com/downloads/produtos/6704/imagem.jpg',	'in_stock',	32.00,	0.00,	'Creatures Comp',	'3065',	'{\"fb\":1,\"gg\":\"23\",\"kk\":\"Calções de Banho\"}',	50,	0.00,	0,	0,	0,	0,	'L',	'new',	'preto',	'kids',	''),
(4,	1,	'LCO006whli',	'10',	'Creatures Comp 6&#39; lime',	'descrição do produto',	'https://rochasurfshop.com/product/swimwear-boys/rip-curl-oceanz-volley/6704',	'https://rochasurfshop.com/downloads/produtos/6704/imagem.jpg',	'in_stock',	32.00,	0.00,	'Creatures Comp',	'3065',	'{\"fb\":1,\"gg\":\"23\",\"kk\":\"Calções de Banho\"}',	50,	0.00,	0,	0,	0,	0,	'L',	'new',	'preto',	'kids',	'');

DROP TABLE IF EXISTS `exportador_pwdReset`;
CREATE TABLE `exportador_pwdReset` (
  `pwdResetId` int(11) NOT NULL AUTO_INCREMENT,
  `pwdResetEmail` varchar(255) NOT NULL,
  `pwdResetSelector` varchar(255) NOT NULL,
  `pwdResetToken` varchar(255) NOT NULL,
  `pwdResetExpires` varchar(255) NOT NULL,
  PRIMARY KEY (`pwdResetId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `exportador_pwdReset` (`pwdResetId`, `pwdResetEmail`, `pwdResetSelector`, `pwdResetToken`, `pwdResetExpires`) VALUES
(94,	'ines@ideiasfrescas.com',	'090f2e266390b0fd',	'$2y$10$92/whYEGxYHoS58yiRGVn.hX89S/M0K.3TaKOgWdwHMSVvffCjeJS',	'1664466454');

DROP TABLE IF EXISTS `exportador_users`;
CREATE TABLE `exportador_users` (
  `id_u` bigint(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `pwd` varchar(64) CHARACTER SET utf8mb4 NOT NULL,
  `permission` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `unique_index` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `exportador_users` (`id_u`, `firstname`, `lastname`, `username`, `email`, `pwd`, `permission`) VALUES
(42,	'admin',	'admin',	'admin',	'ines@ideiasfrescas.com',	'$2y$10$eTjpQiLpIrgkUjTS0JK7nOmfTmvnim3Ovs8DiEq5mxeHCgAkfVJNW',	0);

DROP TABLE IF EXISTS `_produtos_onlinestore`;
CREATE TABLE `_produtos_onlinestore` (
  `id_p` bigint(20) NOT NULL AUTO_INCREMENT,
  `name_p` varchar(255) NOT NULL,
  `description_p` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `category_p` varchar(255) CHARACTER SET utf8 NOT NULL,
  `img_p` varchar(255) CHARACTER SET utf8 NOT NULL,
  `buy_price_p` decimal(10,2) NOT NULL,
  `sell_price_p` decimal(10,2) NOT NULL,
  `stock_p` int(32) NOT NULL,
  `stock_reserved_p` int(32) NOT NULL,
  `availability_p` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_p`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2022-10-12 14:44:36
