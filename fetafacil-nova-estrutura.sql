-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27-Out-2024 às 23:01
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
-- Estrutura da tabela `anulado`
--

CREATE TABLE `anulado` (
  `identificador` int(11) NOT NULL,
  `conta` varchar(500) NOT NULL,
  `operacao` varchar(500) NOT NULL,
  `dados` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `anulado`
--

INSERT INTO `anulado` (`identificador`, `conta`, `operacao`, `dados`) VALUES
(1, '921797626', '671c3f0ce9452', '{\"identificador_conta\":\"6710363e3da27\",\"pid\":\"671c3f0ce9452\",\"tipo\":\"normal\",\"de\":\"921797626\",\"para\":\"947436662\",\"onde\":\"app\",\"valor\":\"500.00\",\"descricao\":\"uma descricao\",\"quando\":\"26-10-2024 02:59:56\",\"dia\":\"26\",\"mes\":\"10\",\"ano\":\"2024\",\"executado\":\"0\",\"pedido\":\"0\"}'),
(2, '947436662', '671c3f0ce9452', '{\"identificador_conta\":\"6710363e3da27\",\"pid\":\"671c3f0ce9452\",\"tipo\":\"normal\",\"de\":\"921797626\",\"para\":\"947436662\",\"onde\":\"app\",\"valor\":\"500.00\",\"descricao\":\"uma descricao\",\"quando\":\"26-10-2024 02:59:56\",\"dia\":\"26\",\"mes\":\"10\",\"ano\":\"2024\",\"executado\":\"0\",\"pedido\":\"0\"}');

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
  `identificador` bigint(20) NOT NULL,
  `cliente_identificador` varchar(255) DEFAULT NULL,
  `codigo_enviado` varchar(255) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `quando` varchar(255) NOT NULL,
  `confirmou` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `confirmar`
--

INSERT INTO `confirmar` (`identificador`, `cliente_identificador`, `codigo_enviado`, `acao`, `quando`, `confirmou`) VALUES
(2, '921797626', '269723', 'cadastro', '1713782106', 0),
(3, '921797626', '231644', 'cadastro', '16-10-2024 22:53:09 PM', 1),
(5, '921797626', '465168', 'codigo', '1729950443', 1);

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
('671039056e3cc', '671039056e390', '921797626', 'nome da empresa', 'uite', '43989.09', 'default.png');

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
(16, '671039056e3cc', '3', 1, '74000.00', '108095.09', '09-09-2024', '09', '09', '2024'),
(21, '6710363e3da27', '671c3cbf59847', 0, '500.00', '1404.91', '26-10-2024 02:50:07', '26', '10', '2024'),
(22, '671039056e3cc', '671c3cbf59847', 1, '500.00', '107595.09', '26-10-2024 02:50:07', '26', '10', '2024'),
(23, '6710363e3da27', '671c3f0ce9452', 0, '500.00', '904.91', '26-10-2024 02:59:56', '26', '10', '2024'),
(24, '671039056e3cc', '671c3f0ce9452', 1, '500.00', '107095.09', '26-10-2024 02:59:56', '26', '10', '2024'),
(25, '6710363e3da27', '671c3f1c122ce', 0, '500.00', '404.91', '26-10-2024 03:00:12', '26', '10', '2024'),
(26, '671039056e3cc', '671c3f1c122ce', 1, '500.00', '106595.09', '26-10-2024 03:00:12', '26', '10', '2024'),
(27, '671039056e3cc', '671c4298464ed', 0, '100000.00', '6595.09', '26-10-2024 03:15:04', '26', '10', '2024'),
(28, '6710363e3da27', '671c4298464ed', 1, '100000.00', '100404.91', '26-10-2024 03:15:04', '26', '10', '2024'),
(29, '671039056e3cc', '671c44ee10587', 0, '100.00', '6495.09', '26-10-2024 03:25:02', '26', '10', '2024'),
(30, '6710363e3da27', '671c44ee10587', 1, '100.00', '100504.91', '26-10-2024 03:25:02', '26', '10', '2024'),
(31, '671039056e3cc', '671c4518677cd', 0, '100.00', '6395.09', '26-10-2024 03:25:44', '26', '10', '2024'),
(32, '6710363e3da27', '671c4518677cd', 1, '100.00', '100604.91', '26-10-2024 03:25:44', '26', '10', '2024'),
(33, '671039056e3cc', '671c452be32f8', 0, '100.00', '6295.09', '26-10-2024 03:26:03', '26', '10', '2024'),
(34, '6710363e3da27', '671c452be32f8', 1, '100.00', '100704.91', '26-10-2024 03:26:03', '26', '10', '2024'),
(35, '671039056e3cc', '671c453b2950b', 0, '100.00', '6195.09', '26-10-2024 03:26:19', '26', '10', '2024'),
(36, '6710363e3da27', '671c453b2950b', 1, '100.00', '100804.91', '26-10-2024 03:26:19', '26', '10', '2024'),
(37, '671039056e3cc', '671c46e720d91', 0, '1000.00', '5195.09', '26-10-2024 03:33:27', '26', '10', '2024'),
(38, '6710363e3da27', '671c46e720d91', 1, '1000.00', '101804.91', '26-10-2024 03:33:27', '26', '10', '2024'),
(39, '671039056e3cc', '671c49364bb7a', 0, '1000.00', '4195.09', '26-10-2024 03:43:18', '26', '10', '2024'),
(40, '6710363e3da27', '671c49364bb7a', 1, '1000.00', '102804.91', '26-10-2024 03:43:18', '26', '10', '2024'),
(41, '671039056e3cc', '671c4abd42b94', 0, '200.00', '3995.09', '26-10-2024 03:49:49', '26', '10', '2024'),
(42, '6710363e3da27', '671c4abd42b94', 1, '200.00', '103004.91', '26-10-2024 03:49:49', '26', '10', '2024'),
(43, '671039056e3cc', '671c4e3e7ecf7', 0, '208.00', '3787.09', '26-10-2024 04:04:46', '26', '10', '2024'),
(44, '6710363e3da27', '671c4e3e7ecf7', 1, '208.00', '103212.91', '26-10-2024 04:04:46', '26', '10', '2024'),
(45, '671039056e3cc', '671c4e6720c8e', 0, '208.00', '3579.09', '26-10-2024 04:05:27', '26', '10', '2024'),
(46, '6710363e3da27', '671c4e6720c8e', 1, '208.00', '103420.91', '26-10-2024 04:05:27', '26', '10', '2024'),
(47, '6710363e3da27', '671cb777663de', 0, '400.00', '103020.91', '26-10-2024 11:33:43', '26', '10', '2024'),
(48, '671039056e3cc', '671cb777663de', 1, '400.00', '3979.09', '26-10-2024 11:33:43', '26', '10', '2024'),
(49, '6710363e3da27', '671cb777663de', 0, '400.00', '102620.91', '26-10-2024 11:33:43', '26', '10', '2024'),
(50, '671039056e3cc', '671cb777663de', 1, '400.00', '4379.09', '26-10-2024 11:33:43', '26', '10', '2024'),
(51, '671039056e3cc', '671e9a7ca8b32', 0, '30.00', '4349.09', '27-10-2024 08:54:36', '27', '10', '2024'),
(52, '6710363e3da27', '671e9a7ca8b32', 1, '30.00', '102650.91', '27-10-2024 08:54:36', '27', '10', '2024'),
(53, '671039056e3cc', '671e9ac713b7a', 0, '30.00', '4319.09', '27-10-2024 08:55:51', '27', '10', '2024'),
(54, '6710363e3da27', '671e9ac713b7a', 1, '30.00', '102680.91', '27-10-2024 08:55:51', '27', '10', '2024'),
(55, '671039056e3cc', '671e9af4b6cc0', 0, '30.00', '4289.09', '27-10-2024 08:56:36', '27', '10', '2024'),
(56, '6710363e3da27', '671e9af4b6cc0', 1, '30.00', '102710.91', '27-10-2024 08:56:36', '27', '10', '2024'),
(57, '6710363e3da27', '671e9d24586fb', 0, '900.00', '101810.91', '27-10-2024 09:05:56', '27', '10', '2024'),
(58, '671039056e3cc', '671e9d24586fb', 1, '900.00', '5189.09', '27-10-2024 09:05:56', '27', '10', '2024'),
(59, '6710363e3da27', '671e9d576a4c8', 0, '900.00', '100910.91', '27-10-2024 09:06:47', '27', '10', '2024'),
(60, '671039056e3cc', '671e9d576a4c8', 1, '900.00', '6089.09', '27-10-2024 09:06:47', '27', '10', '2024'),
(61, '6710363e3da27', '671e9d58d14d4', 0, '900.00', '100010.91', '27-10-2024 09:06:48', '27', '10', '2024'),
(62, '671039056e3cc', '671e9d58d14d4', 1, '900.00', '6989.09', '27-10-2024 09:06:48', '27', '10', '2024'),
(63, '6710363e3da27', '671e9d5a24ee9', 0, '900.00', '99110.91', '27-10-2024 09:06:50', '27', '10', '2024'),
(64, '671039056e3cc', '671e9d5a24ee9', 1, '900.00', '7889.09', '27-10-2024 09:06:50', '27', '10', '2024'),
(65, '671039056e3cc', '671e9d6fa483b', 0, '100.00', '7789.09', '27-10-2024 09:07:11', '27', '10', '2024'),
(66, '6710363e3da27', '671e9d6fa483b', 1, '100.00', '99210.91', '27-10-2024 09:07:11', '27', '10', '2024'),
(67, '6710363e3da27', '671e9da560dc0', 0, '400.00', '98810.91', '27-10-2024 09:08:05', '27', '10', '2024'),
(68, '671039056e3cc', '671e9da560dc0', 1, '400.00', '8189.09', '27-10-2024 09:08:05', '27', '10', '2024'),
(69, '6710363e3da27', '671e9dffe6b58', 0, '900.00', '97910.91', '27-10-2024 09:09:35', '27', '10', '2024'),
(70, '671039056e3cc', '671e9dffe6b58', 1, '900.00', '9089.09', '27-10-2024 09:09:35', '27', '10', '2024'),
(71, '6710363e3da27', '671e9e787a9aa', 0, '900.00', '97010.91', '27-10-2024 09:11:36', '27', '10', '2024'),
(72, '671039056e3cc', '671e9e787a9aa', 1, '900.00', '9989.09', '27-10-2024 09:11:36', '27', '10', '2024'),
(73, '671039056e3cc', '671e9e789fa9b', 0, '100.00', '9889.09', '27-10-2024 09:11:36', '27', '10', '2024'),
(74, '6710363e3da27', '671e9e789fa9b', 1, '100.00', '97110.91', '27-10-2024 09:11:36', '27', '10', '2024'),
(75, '6710363e3da27', '671e9e78b7da6', 0, '800.00', '96310.91', '27-10-2024 09:11:36', '27', '10', '2024'),
(76, '671039056e3cc', '671e9e78b7da6', 1, '800.00', '10689.09', '27-10-2024 09:11:36', '27', '10', '2024'),
(77, '6710363e3da27', '671e9e78c6d97', 0, '400.00', '95910.91', '27-10-2024 09:11:36', '27', '10', '2024'),
(78, '671039056e3cc', '671e9e78c6d97', 1, '400.00', '11089.09', '27-10-2024 09:11:36', '27', '10', '2024'),
(79, '6710363e3da27', '671e9e78e275e', 0, '15000.00', '80910.91', '27-10-2024 09:11:36', '27', '10', '2024'),
(80, '671039056e3cc', '671e9e78e275e', 1, '15000.00', '26089.09', '27-10-2024 09:11:36', '27', '10', '2024'),
(81, '6710363e3da27', '671e9e8c74553', 0, '900.00', '80010.91', '27-10-2024 09:11:56', '27', '10', '2024'),
(82, '671039056e3cc', '671e9e8c74553', 1, '900.00', '26989.09', '27-10-2024 09:11:56', '27', '10', '2024'),
(83, '6710363e3da27', '671e9f4a4c1e6', 0, '900.00', '79110.91', '27-10-2024 09:15:06', '27', '10', '2024'),
(84, '671039056e3cc', '671e9f4a4c1e6', 1, '900.00', '27889.09', '27-10-2024 09:15:06', '27', '10', '2024'),
(85, '671039056e3cc', '671e9f4a661ae', 0, '100.00', '27789.09', '27-10-2024 09:15:06', '27', '10', '2024'),
(86, '6710363e3da27', '671e9f4a661ae', 1, '100.00', '79210.91', '27-10-2024 09:15:06', '27', '10', '2024'),
(87, '6710363e3da27', '671e9f4a7801a', 0, '800.00', '78410.91', '27-10-2024 09:15:06', '27', '10', '2024'),
(88, '671039056e3cc', '671e9f4a7801a', 1, '800.00', '28589.09', '27-10-2024 09:15:06', '27', '10', '2024'),
(89, '6710363e3da27', '671e9f4a8bedb', 0, '400.00', '78010.91', '27-10-2024 09:15:06', '27', '10', '2024'),
(90, '671039056e3cc', '671e9f4a8bedb', 1, '400.00', '28989.09', '27-10-2024 09:15:06', '27', '10', '2024'),
(91, '6710363e3da27', '671e9f4aabefb', 0, '15000.00', '63010.91', '27-10-2024 09:15:06', '27', '10', '2024'),
(92, '671039056e3cc', '671e9f4aabefb', 1, '15000.00', '43989.09', '27-10-2024 09:15:06', '27', '10', '2024');

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
('1099985634', '[6,\"671e9f4a4c1e6\"]', '921797626', '947436662', '5', '900.00', '4500.00', 'semanal', '01-10-2024', '01', '10', '2024', 1),
('671c4e6721635', '[\"671c4e6720c8e\",\"671e9f4a661ae\"]', '947436662', '921797626', '5', '100.00', '200.00', 'diario', '26-10-2024 04:05:27', '26', '10', '2024', 1),
('671cb63b12ed2', '[\"671cb63b11a39\",\"671e9f4a7801a\"]', '921797626', '947436662', '5', '800.00', '400.00', 'diario', '26-10-2024 11:28:27', '26', '10', '2024', 1),
('671cb77766e5e', '[\"671e9da560dc0\",\"671e9f4a8bedb\"]', '921797626', '947436662', '5', '400.00', '800.00', 'diario', '26-10-2024 11:33:43', '26', '10', '2024', 1),
('671cc12adf664', '[\"671cc129df3f1\",\"671e9f4aabefb\"]', '921797626', '947436662', '5', '15000.00', '30000.00', 'diario', '26-10-2024 12:15:05', '26', '10', '2024', 1),
('671cc18a9860f', '[\"671cc18a97505\"]', '921797626', '947436662', '5', '150000.00', '300000.00', 'diario', '26-10-2024 12:16:42', '26', '10', '2024', 1);

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
('6710363e3da27', '6710363e3da0a', 'AH09765345O45', 'nome da empresa', 'm', '15-08-1996', '63010.91', 'default.png');

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
('1785469045', '[7]', '921797626', '947436662', '70.00', 'mensal', '07-10-2024', '07', '10', '2024', 1),
('671039056e3cc', '[\"671c49364bb7a\"]', '947436662', '921797626', '1000.00', 'diario', '26-10-2024 03:43:18', '26', '10', '2024', 1),
('671c4abd437aa', '[\"671c4abd42b94\"]', '947436662', '921797626', '200.00', 'diario', '26-10-2024 03:49:49', '26', '10', '2024', 1),
('671c4e3e7f6d3', '[\"671c4e3e7ecf7\"]', '947436662', '921797626', '208.00', 'diario', '26-10-2024 04:04:46', '26', '10', '2024', 1),
('671ca86905ac6', '[\"671ca8690503d\"]', '921797626', '947436662', '10.00', 'diario', '26-10-2024 10:29:29', '26', '10', '2024', 0);

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
  `executado` tinyint(1) NOT NULL,
  `pedido` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `transacao`
--

INSERT INTO `transacao` (`identificador_conta`, `pid`, `tipo`, `de`, `para`, `onde`, `valor`, `descricao`, `quando`, `dia`, `mes`, `ano`, `executado`, `pedido`) VALUES
('6710363e3da27', '1', 'normal', '921797626', '947436662', 'app', '7000.09', 'urgente', '07-09-2024', '07', '09', '2024', 1, 0),
('671039056e3cc', '10', 'normal', '947436662', '921797626', 'app', '500.00', 'urgente', '18-10-2024', '18', '10', '2024', 0, 0),
('6710363e3da27', '2', 'normal', '921797626', '947436662', 'app', '17000.00', 'urgente', '08-09-2024', '08', '09', '2024', 1, 0),
('6710363e3da27', '3', 'normal', '921797626', '947436662', 'app', '74000.00', 'urgente', '09-09-2024', '09', '09', '2024', 1, 0),
('6710363e3da27', '4', 'normal', '921797626', '947436662', 'app', '2000.00', 'urgente', '15-09-2024', '15', '09', '2024', 1, 0),
('6710363e3da27', '5', 'normal', '921797626', '947436662', 'app', '1500.00', 'urgente', '01-10-2024', '01', '10', '2024', 1, 0),
('6710363e3da27', '6', 'parcelado', '921797626', '947436662', 'app', '900.00', 'urgente', '01-10-2024', '01', '10', '2024', 1, 0),
('6710363e3da27', '671c3cbf59847', 'normal', '921797626', '947436662', 'app', '500.00', 'uma descricao', '26-10-2024 02:50:07', '26', '10', '2024', 0, 0),
('6710363e3da27', '671c3f1c122ce', 'normal', '921797626', '947436662', 'app', '500.00', 'uma descricao', '26-10-2024 03:00:12', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c4298464ed', 'normal', '947436662', '921797626', 'app', '100000.00', 'uma descricao', '26-10-2024 03:15:04', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c44ee10587', 'normal', '947436662', '921797626', 'app', '100.00', 'uma descricao', '26-10-2024 03:25:02', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c4518677cd', 'normal', '947436662', '921797626', 'app', '100.00', 'uma descricao', '26-10-2024 03:25:44', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c452be32f8', 'normal', '947436662', '921797626', 'app', '100.00', 'uma descricao', '26-10-2024 03:26:03', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c453b2950b', 'normal', '947436662', '921797626', 'app', '100.00', 'uma descricao', '26-10-2024 03:26:19', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c46e720d91', 'normal', '947436662', '921797626', 'app', '1000.00', 'uma descricao', '26-10-2024 03:33:27', '26', '10', '2024', 0, 0),
('671039056e3cc', '671c49364bb7a', 'recorrente', '947436662', '921797626', 'app', '1000.00', 'uma descricao', '26-10-2024 03:43:18', '26', '10', '2024', 1, 0),
('671039056e3cc', '671c4abd42b94', 'recorrente', '947436662', '921797626', 'app', '200.00', 'uma descricao', '26-10-2024 03:49:49', '26', '10', '2024', 1, 0),
('671039056e3cc', '671c4e3e7ecf7', 'recorrente', '947436662', '921797626', 'app', '208.00', 'uma descricao', '26-10-2024 04:04:46', '26', '10', '2024', 1, 0),
('671039056e3cc', '671c4e6720c8e', 'parcelado', '947436662', '921797626', 'app', '100.00', 'uma descricao', '26-10-2024 04:05:27', '26', '10', '2024', 1, 0),
('671039056e3cc', '671ca8690503d', 'recorrente', '921797626', '947436662', 'app', '10.00', 'uma descricao', '26-10-2024 10:29:29', '26', '10', '2024', 0, 1),
('671039056e3cc', '671cb63b11a39', 'parcelado', '921797626', '947436662', 'app', '800.00', 'uma descricao', '26-10-2024 11:28:27', '26', '10', '2024', 0, 1),
('671039056e3cc', '671cb777663de', 'parcelado', '921797626', '947436662', 'app', '400.00', 'uma descricao', '26-10-2024 11:33:43', '26', '10', '2024', 1, 1),
('671039056e3cc', '671cc129df3f1', 'parcelado', '921797626', '947436662', 'app', '15000.00', 'uma descricao', '26-10-2024 12:15:05', '26', '10', '2024', 0, 1),
('671039056e3cc', '671cc18a97505', 'parcelado', '921797626', '947436662', 'app', '150000.00', 'uma descricao', '26-10-2024 12:16:42', '26', '10', '2024', 0, 1),
('671039056e3cc', '671e9a7ca8b32', 'normal', '947436662', '921797626', 'app', '30.00', 'uma descricao', '27-10-2024 08:54:36', '27', '10', '2024', 0, 0),
('671039056e3cc', '671e9ac713b7a', 'normal', '947436662', '921797626', 'app', '30.00', 'uma descricao', '27-10-2024 08:55:51', '27', '10', '2024', 0, 0),
('671039056e3cc', '671e9af4b6cc0', 'normal', '947436662', '921797626', 'app', '30.00', 'uma descricao', '27-10-2024 08:56:36', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9d24586fb', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:05:56', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9d576a4c8', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:06:47', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9d58d14d4', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:06:48', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9d5a24ee9', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:06:50', '27', '10', '2024', 1, 0),
('671039056e3cc', '671e9d6fa483b', 'normal', '947436662', '921797626', 'system', '100.00', 'Pagamento automatico de parcela', '27-10-2024 09:07:11', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9da560dc0', 'normal', '921797626', '947436662', 'system', '400.00', 'Pagamento automatico de parcela', '27-10-2024 09:08:05', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9dffe6b58', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:09:35', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9e787a9aa', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:36', '27', '10', '2024', 1, 0),
('671039056e3cc', '671e9e789fa9b', 'normal', '947436662', '921797626', 'system', '100.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:36', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9e78b7da6', 'normal', '921797626', '947436662', 'system', '800.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:36', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9e78c6d97', 'normal', '921797626', '947436662', 'system', '400.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:36', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9e78e275e', 'normal', '921797626', '947436662', 'system', '15000.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:36', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9e8c74553', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:11:56', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9f4a4c1e6', 'normal', '921797626', '947436662', 'system', '900.00', 'Pagamento automatico de parcela', '27-10-2024 09:15:06', '27', '10', '2024', 1, 0),
('671039056e3cc', '671e9f4a661ae', 'normal', '947436662', '921797626', 'system', '100.00', 'Pagamento automatico de parcela', '27-10-2024 09:15:06', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9f4a7801a', 'normal', '921797626', '947436662', 'system', '800.00', 'Pagamento automatico de parcela', '27-10-2024 09:15:06', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9f4a8bedb', 'normal', '921797626', '947436662', 'system', '400.00', 'Pagamento automatico de parcela', '27-10-2024 09:15:06', '27', '10', '2024', 1, 0),
('6710363e3da27', '671e9f4aabefb', 'normal', '921797626', '947436662', 'system', '15000.00', 'Pagamento automatico de parcela', '27-10-2024 09:15:06', '27', '10', '2024', 1, 0),
('6710363e3da27', '7', 'recorrente', '921797626', '947436662', 'app', '70.00', 'urgente', '07-10-2024', '07', '10', '2024', 1, 0),
('6710363e3da27', '8', 'normal', '921797626', '947436662', 'app', '5625.00', 'urgente', '17-10-2024', '17', '10', '2024', 1, 0),
('6710363e3da27', '9', 'normal', '921797626', '947436662', 'app', '500.00', 'urgente', '18-10-2024', '18', '10', '2024', 0, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `anulado`
--
ALTER TABLE `anulado`
  ADD PRIMARY KEY (`identificador`);

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
-- Índices para tabela `confirmar`
--
ALTER TABLE `confirmar`
  ADD PRIMARY KEY (`identificador`);

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
-- AUTO_INCREMENT de tabela `anulado`
--
ALTER TABLE `anulado`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `confirmar`
--
ALTER TABLE `confirmar`
  MODIFY `identificador` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de tabela `levantamento`
--
ALTER TABLE `levantamento`
  MODIFY `identificador` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
