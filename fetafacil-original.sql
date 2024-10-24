-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24-Out-2024 às 20:38
-- Versão do servidor: 10.4.24-MariaDB
-- versão do PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `fetafacil`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente`
--

CREATE TABLE `cliente` (
  `identificador` varchar(500) NOT NULL,
  `empresa` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`identificador`, `empresa`) VALUES
('6710363e3da0a', 0),
('671039056e390', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracao`
--

CREATE TABLE `configuracao` (
  `identificador` int(11) NOT NULL,
  `cliente_identificador` text NOT NULL,
  `tempo_bloqueio` int(11) NOT NULL,
  `pin` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `configuracao`
--

INSERT INTO `configuracao` (`identificador`, `cliente_identificador`, `tempo_bloqueio`, `pin`) VALUES
(2, '6710363e3da0a', 30, 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413'),
(3, '671039056e390', 30, 'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413');

-- --------------------------------------------------------

--
-- Estrutura da tabela `confirmar`
--

CREATE TABLE `confirmar` (
  `cliente_identificador` varchar(255) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `codigo_enviado` varchar(255) DEFAULT NULL,
  `quando` varchar(255) NOT NULL,
  `confirmou` tinyint(1) NOT NULL,
  `identificador` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `confirmar`
--

INSERT INTO `confirmar` (`cliente_identificador`, `acao`, `codigo_enviado`, `quando`, `confirmou`, `identificador`) VALUES
('921797626', 'cadastro', '269723', '16-10-2024 22:50:54 PM', 0, 2),
('921797626', 'cadastro', '231644', '16-10-2024 22:53:09 PM', 1, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `contacto`
--

CREATE TABLE `contacto` (
  `identificador` int(11) NOT NULL,
  `cliente_identificador` text NOT NULL,
  `telefone` text NOT NULL,
  `email` text DEFAULT NULL,
  `atual` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `contacto`
--

INSERT INTO `contacto` (`identificador`, `cliente_identificador`, `telefone`, `email`, `atual`) VALUES
(1, '6710363e3da0a', '921797626', NULL, 1),
(2, '671039056e390', '947436662', NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `deposito`
--

CREATE TABLE `deposito` (
  `identificador` int(11) NOT NULL,
  `identificador_conta` varchar(255) NOT NULL,
  `transacao_pid` varchar(500) NOT NULL,
  `agente` text NOT NULL,
  `notas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`notas`)),
  `total` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE `empresa` (
  `identificador` varchar(500) NOT NULL,
  `cliente_identificador` varchar(500) NOT NULL,
  `nif` text NOT NULL,
  `nome` text NOT NULL,
  `area_atuacao` text NOT NULL,
  `balanco` decimal(16,2) DEFAULT NULL,
  `img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`identificador`, `cliente_identificador`, `nif`, `nome`, `area_atuacao`, `balanco`, `img`) VALUES
('671039056e3cc', '671039056e390', '921797626', 'nome da empresa', 'uite', '108095.09', 'default.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `endereco`
--

CREATE TABLE `endereco` (
  `identificador` int(11) NOT NULL,
  `cliente_identificador` varchar(255) NOT NULL,
  `provincia` text DEFAULT NULL,
  `cidade` text DEFAULT NULL,
  `bairro` text DEFAULT NULL,
  `atual` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `endereco`
--

INSERT INTO `endereco` (`identificador`, `cliente_identificador`, `provincia`, `cidade`, `bairro`, `atual`) VALUES
(1, '6710363e3da0a', NULL, NULL, NULL, 1),
(2, '671039056e390', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `extrato`
--

CREATE TABLE `extrato` (
  `identificador` int(11) NOT NULL,
  `identificador_conta` text NOT NULL,
  `transacao_pid` text NOT NULL,
  `entrada` tinyint(1) NOT NULL,
  `movimento` decimal(16,2) NOT NULL,
  `balanco` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `extrato`
--

INSERT INTO `extrato` (`identificador`, `identificador_conta`, `transacao_pid`, `entrada`, `movimento`, `balanco`, `quando`, `dia`, `mes`, `ano`) VALUES
(1, '6710363e3da27', '8', 0, '5625.00', '104375.00', '17-10-2024', '17', '10', '2024'),
(2, '6710363e3da27', '1', 0, '7000.09', '97374.91', '07-09-2024', '07', '09', '2024'),
(3, '6710363e3da27', '7', 0, '70.00', '97304.91', '07-10-2024', '07', '10', '2024'),
(4, '6710363e3da27', '6', 0, '900.00', '96404.91', '01-10-2024', '01', '10', '2024'),
(5, '6710363e3da27', '5', 0, '1500.00', '94904.91', '01-10-2024', '01', '10', '2024'),
(6, '6710363e3da27', '4', 0, '2000.00', '92904.91', '15-09-2024', '15', '09', '2024'),
(7, '6710363e3da27', '2', 0, '17000.00', '75904.91', '08-09-2024', '08', '09', '2024'),
(8, '6710363e3da27', '3', 0, '74000.00', '1904.91', '09-09-2024', '09', '09', '2024'),
(9, '671039056e3cc', '8', 1, '5625.00', '5625.00', '17-10-2024', '17', '10', '2024'),
(10, '671039056e3cc', '1', 1, '7000.09', '12625.09', '07-09-2024', '07', '09', '2024'),
(11, '671039056e3cc', '7', 1, '70.00', '12695.09', '07-10-2024', '07', '10', '2024'),
(12, '671039056e3cc', '6', 1, '900.00', '13595.09', '01-10-2024', '01', '10', '2024'),
(13, '671039056e3cc', '5', 1, '1500.00', '15095.09', '01-10-2024', '01', '10', '2024'),
(14, '671039056e3cc', '4', 1, '2000.00', '17095.09', '15-09-2024', '15', '09', '2024'),
(15, '671039056e3cc', '2', 1, '17000.00', '34095.09', '08-09-2024', '08', '09', '2024'),
(16, '671039056e3cc', '3', 1, '74000.00', '108095.09', '09-09-2024', '09', '09', '2024');

-- --------------------------------------------------------

--
-- Estrutura da tabela `levantamento`
--

CREATE TABLE `levantamento` (
  `identificador` int(11) NOT NULL,
  `identificador_conta` varchar(255) NOT NULL,
  `transacao_pid` varchar(500) NOT NULL,
  `agente` text NOT NULL,
  `notas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`notas`)),
  `total` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `parcelado`
--

CREATE TABLE `parcelado` (
  `identificador` varchar(500) NOT NULL,
  `transacao_pid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`transacao_pid`)),
  `de` varchar(255) NOT NULL,
  `para` varchar(255) NOT NULL,
  `parcelas` text NOT NULL,
  `valor_parcela` decimal(16,2) NOT NULL,
  `valor_total` decimal(16,2) NOT NULL,
  `periodicidade` varchar(255) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `parcelado`
--

INSERT INTO `parcelado` (`identificador`, `transacao_pid`, `de`, `para`, `parcelas`, `valor_parcela`, `valor_total`, `periodicidade`, `quando`, `dia`, `mes`, `ano`, `ativo`) VALUES
('1099985634', '[6]', '921797626', '947436662', '5', '900.00', '4500.00', 'semanal', '01-10-2024', '01', '10', '2024', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `particular`
--

CREATE TABLE `particular` (
  `identificador` varchar(500) NOT NULL,
  `cliente_identificador` varchar(500) NOT NULL,
  `bi` text DEFAULT NULL,
  `nome` text NOT NULL,
  `genero` text DEFAULT NULL,
  `nascimento` text DEFAULT NULL,
  `balanco` decimal(16,2) NOT NULL,
  `img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `particular`
--

INSERT INTO `particular` (`identificador`, `cliente_identificador`, `bi`, `nome`, `genero`, `nascimento`, `balanco`, `img`) VALUES
('6710363e3da27', '6710363e3da0a', 'AH09765345O45', 'nome da empresa', 'm', '15-08-1996', '1904.91', 'default.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `recorrente`
--

CREATE TABLE `recorrente` (
  `identificador` varchar(500) NOT NULL,
  `transacao_pid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`transacao_pid`)),
  `de` varchar(255) NOT NULL,
  `para` varchar(255) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  `periodicidade` text NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL,
  `ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `recorrente`
--

INSERT INTO `recorrente` (`identificador`, `transacao_pid`, `de`, `para`, `valor`, `periodicidade`, `quando`, `dia`, `mes`, `ano`, `ativo`) VALUES
('1785469045', '[7]', '921797626', '947436662', '70.00', 'mensal', '07-10-2024', '07', '10', '2024', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `transacao`
--

CREATE TABLE `transacao` (
  `identificador_conta` varchar(255) DEFAULT NULL,
  `pid` varchar(500) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `de` varchar(500) NOT NULL,
  `para` varchar(500) NOT NULL,
  `onde` varchar(500) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  `descricao` varchar(500) NOT NULL,
  `quando` varchar(500) NOT NULL,
  `dia` varchar(255) NOT NULL,
  `mes` varchar(255) NOT NULL,
  `ano` varchar(255) NOT NULL,
  `executado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `transacao`
--

INSERT INTO `transacao` (`identificador_conta`, `pid`, `tipo`, `de`, `para`, `onde`, `valor`, `descricao`, `quando`, `dia`, `mes`, `ano`, `executado`) VALUES
('6710363e3da27', '1', 'normal', '921797626', '947436662', 'app', '7000.09', 'urgente', '07-09-2024', '07', '09', '2024', 1),
('671039056e3cc', '10', 'normal', '947436662', '921797626', 'app', '500.00', 'urgente', '18-10-2024', '18', '10', '2024', 0),
('6710363e3da27', '2', 'normal', '921797626', '947436662', 'app', '17000.00', 'urgente', '08-09-2024', '08', '09', '2024', 1),
('6710363e3da27', '3', 'normal', '921797626', '947436662', 'app', '74000.00', 'urgente', '09-09-2024', '09', '09', '2024', 1),
('6710363e3da27', '4', 'normal', '921797626', '947436662', 'app', '2000.00', 'urgente', '15-09-2024', '15', '09', '2024', 1),
('6710363e3da27', '5', 'normal', '921797626', '947436662', 'app', '1500.00', 'urgente', '01-10-2024', '01', '10', '2024', 1),
('6710363e3da27', '6', 'parcelado', '921797626', '947436662', 'app', '900.00', 'urgente', '01-10-2024', '01', '10', '2024', 1),
('6710363e3da27', '7', 'recorrente', '921797626', '947436662', 'app', '70.00', 'urgente', '07-10-2024', '07', '10', '2024', 1),
('6710363e3da27', '8', 'normal', '921797626', '947436662', 'app', '5625.00', 'urgente', '17-10-2024', '17', '10', '2024', 1),
('6710363e3da27', '9', 'normal', '921797626', '947436662', 'app', '500.00', 'urgente', '18-10-2024', '18', '10', '2024', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`identificador`);

--
-- Índices para tabela `configuracao`
--
ALTER TABLE `configuracao`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `cliente_identificador` (`cliente_identificador`) USING HASH;

--
-- Índices para tabela `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `cliente_identificador` (`cliente_identificador`) USING HASH;

--
-- Índices para tabela `deposito`
--
ALTER TABLE `deposito`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `transacao_pid` (`transacao_pid`);

--
-- Índices para tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `cliente_identificador` (`cliente_identificador`);

--
-- Índices para tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `cliente_identificador` (`cliente_identificador`);

--
-- Índices para tabela `extrato`
--
ALTER TABLE `extrato`
  ADD PRIMARY KEY (`identificador`);

--
-- Índices para tabela `levantamento`
--
ALTER TABLE `levantamento`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `transacao_pid` (`transacao_pid`);

--
-- Índices para tabela `parcelado`
--
ALTER TABLE `parcelado`
  ADD PRIMARY KEY (`identificador`);

--
-- Índices para tabela `particular`
--
ALTER TABLE `particular`
  ADD PRIMARY KEY (`identificador`),
  ADD UNIQUE KEY `cliente_identificador` (`cliente_identificador`);

--
-- Índices para tabela `recorrente`
--
ALTER TABLE `recorrente`
  ADD PRIMARY KEY (`identificador`);

--
-- Índices para tabela `transacao`
--
ALTER TABLE `transacao`
  ADD UNIQUE KEY `pid` (`pid`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `contacto`
--
ALTER TABLE `contacto`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `deposito`
--
ALTER TABLE `deposito`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `extrato`
--
ALTER TABLE `extrato`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `levantamento`
--
ALTER TABLE `levantamento`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
