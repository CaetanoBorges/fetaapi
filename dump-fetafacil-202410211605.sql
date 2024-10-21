-- MariaDB dump 10.19  Distrib 10.4.25-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: fetafacil
-- ------------------------------------------------------
-- Server version	10.4.25-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cliente` (
  `identificador` varchar(500) NOT NULL,
  `empresa` tinyint(1) NOT NULL,
  PRIMARY KEY (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES ('6710363e3da0a',0),('671039056e390',1);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracao`
--

DROP TABLE IF EXISTS `configuracao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracao` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_identificador` text NOT NULL,
  `tempo_bloqueio` int(11) NOT NULL,
  `auto_pagamento_recebimento` tinyint(1) NOT NULL,
  `pin` text NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `cliente_identificador` (`cliente_identificador`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracao`
--

LOCK TABLES `configuracao` WRITE;
/*!40000 ALTER TABLE `configuracao` DISABLE KEYS */;
INSERT INTO `configuracao` VALUES (2,'6710363e3da0a',30,0,'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413'),(3,'671039056e390',30,0,'ba3253876aed6bc22d4a6ff53d8406c6ad864195ed144ab5c87621b6c233b548baeae6956df346ec8c17f5ea10f35ee3cbc514797ed7ddd3145464e2a0bab413');
/*!40000 ALTER TABLE `configuracao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confirmar`
--

DROP TABLE IF EXISTS `confirmar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `confirmar` (
  `cliente_identificador` varchar(255) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `codigo_enviado` varchar(255) DEFAULT NULL,
  `quando` varchar(255) NOT NULL,
  `confirmou` tinyint(1) NOT NULL,
  `identificador` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confirmar`
--

LOCK TABLES `confirmar` WRITE;
/*!40000 ALTER TABLE `confirmar` DISABLE KEYS */;
INSERT INTO `confirmar` VALUES ('921797626','cadastro','269723','16-10-2024 22:50:54 PM',0,2),('921797626','cadastro','231644','16-10-2024 22:53:09 PM',1,3);
/*!40000 ALTER TABLE `confirmar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacto`
--

DROP TABLE IF EXISTS `contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacto` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_identificador` text NOT NULL,
  `telefone` text NOT NULL,
  `email` text DEFAULT NULL,
  `atual` tinyint(1) NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `cliente_identificador` (`cliente_identificador`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto`
--

LOCK TABLES `contacto` WRITE;
/*!40000 ALTER TABLE `contacto` DISABLE KEYS */;
INSERT INTO `contacto` VALUES (1,'6710363e3da0a','921797626',NULL,1),(2,'671039056e390','947436662',NULL,1);
/*!40000 ALTER TABLE `contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposito`
--

DROP TABLE IF EXISTS `deposito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deposito` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `identificador_conta` varchar(255) NOT NULL,
  `transacao_pid` varchar(500) NOT NULL,
  `agente` text NOT NULL,
  `notas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`notas`)),
  `total` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `transacao_pid` (`transacao_pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposito`
--

LOCK TABLES `deposito` WRITE;
/*!40000 ALTER TABLE `deposito` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresa` (
  `identificador` varchar(500) NOT NULL,
  `cliente_identificador` varchar(500) NOT NULL,
  `nif` text NOT NULL,
  `nome` text NOT NULL,
  `area_atuacao` text NOT NULL,
  `balanco` decimal(16,2) DEFAULT NULL,
  `foto` text NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `cliente_identificador` (`cliente_identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES ('671039056e3cc','671039056e390','921797626','nome da empresa','uite',108095.09,'default.png');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `endereco` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_identificador` varchar(255) NOT NULL,
  `provincia` text DEFAULT NULL,
  `cidade` text DEFAULT NULL,
  `bairro` text DEFAULT NULL,
  `atual` tinyint(1) NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `cliente_identificador` (`cliente_identificador`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,'6710363e3da0a',NULL,NULL,NULL,1),(2,'671039056e390',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extrato`
--

DROP TABLE IF EXISTS `extrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extrato` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `identificador_conta` text NOT NULL,
  `transacao_pid` text NOT NULL,
  `entrada` tinyint(1) NOT NULL,
  `movimento` decimal(16,2) NOT NULL,
  `balanco` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL,
  PRIMARY KEY (`identificador`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extrato`
--

LOCK TABLES `extrato` WRITE;
/*!40000 ALTER TABLE `extrato` DISABLE KEYS */;
INSERT INTO `extrato` VALUES (1,'6710363e3da27','8',0,5625.00,104375.00,'17-10-2024','17','10','2024'),(2,'6710363e3da27','1',0,7000.09,97374.91,'07-09-2024','07','09','2024'),(3,'6710363e3da27','7',0,70.00,97304.91,'07-10-2024','07','10','2024'),(4,'6710363e3da27','6',0,900.00,96404.91,'01-10-2024','01','10','2024'),(5,'6710363e3da27','5',0,1500.00,94904.91,'01-10-2024','01','10','2024'),(6,'6710363e3da27','4',0,2000.00,92904.91,'15-09-2024','15','09','2024'),(7,'6710363e3da27','2',0,17000.00,75904.91,'08-09-2024','08','09','2024'),(8,'6710363e3da27','3',0,74000.00,1904.91,'09-09-2024','09','09','2024'),(9,'671039056e3cc','8',1,5625.00,5625.00,'17-10-2024','17','10','2024'),(10,'671039056e3cc','1',1,7000.09,12625.09,'07-09-2024','07','09','2024'),(11,'671039056e3cc','7',1,70.00,12695.09,'07-10-2024','07','10','2024'),(12,'671039056e3cc','6',1,900.00,13595.09,'01-10-2024','01','10','2024'),(13,'671039056e3cc','5',1,1500.00,15095.09,'01-10-2024','01','10','2024'),(14,'671039056e3cc','4',1,2000.00,17095.09,'15-09-2024','15','09','2024'),(15,'671039056e3cc','2',1,17000.00,34095.09,'08-09-2024','08','09','2024'),(16,'671039056e3cc','3',1,74000.00,108095.09,'09-09-2024','09','09','2024');
/*!40000 ALTER TABLE `extrato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `levantamento`
--

DROP TABLE IF EXISTS `levantamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `levantamento` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `identificador_conta` varchar(255) NOT NULL,
  `transacao_pid` varchar(500) NOT NULL,
  `agente` text NOT NULL,
  `notas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`notas`)),
  `total` decimal(16,2) NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `transacao_pid` (`transacao_pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `levantamento`
--

LOCK TABLES `levantamento` WRITE;
/*!40000 ALTER TABLE `levantamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `levantamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parcelado`
--

DROP TABLE IF EXISTS `parcelado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parcelado` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
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
  `ativo` tinyint(1) NOT NULL,
  PRIMARY KEY (`identificador`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parcelado`
--

LOCK TABLES `parcelado` WRITE;
/*!40000 ALTER TABLE `parcelado` DISABLE KEYS */;
INSERT INTO `parcelado` VALUES (1,'[6]','921797626','947436662','5',900.00,4500.00,'semanal','01-10-2024','01','10','2024',1);
/*!40000 ALTER TABLE `parcelado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `particular`
--

DROP TABLE IF EXISTS `particular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `particular` (
  `identificador` varchar(500) NOT NULL,
  `cliente_identificador` varchar(500) NOT NULL,
  `bi` text DEFAULT NULL,
  `nome` text NOT NULL,
  `genero` text DEFAULT NULL,
  `nascimento` text DEFAULT NULL,
  `balanco` decimal(16,2) NOT NULL,
  `foto` text NOT NULL,
  PRIMARY KEY (`identificador`),
  UNIQUE KEY `cliente_identificador` (`cliente_identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `particular`
--

LOCK TABLES `particular` WRITE;
/*!40000 ALTER TABLE `particular` DISABLE KEYS */;
INSERT INTO `particular` VALUES ('6710363e3da27','6710363e3da0a','AH09765345O45','nome da empresa','m','15-08-1996',1904.91,'default.png');
/*!40000 ALTER TABLE `particular` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recorrente`
--

DROP TABLE IF EXISTS `recorrente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recorrente` (
  `identificador` int(11) NOT NULL AUTO_INCREMENT,
  `transacao_pid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`transacao_pid`)),
  `de` varchar(255) NOT NULL,
  `para` varchar(255) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  `periodicidade` text NOT NULL,
  `quando` text NOT NULL,
  `dia` text NOT NULL,
  `mes` text NOT NULL,
  `ano` text NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  PRIMARY KEY (`identificador`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorrente`
--

LOCK TABLES `recorrente` WRITE;
/*!40000 ALTER TABLE `recorrente` DISABLE KEYS */;
INSERT INTO `recorrente` VALUES (1,'[7]','921797626','947436662',70.00,'mensal','07-10-2024','07','10','2024',1);
/*!40000 ALTER TABLE `recorrente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transacao`
--

DROP TABLE IF EXISTS `transacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  UNIQUE KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transacao`
--

LOCK TABLES `transacao` WRITE;
/*!40000 ALTER TABLE `transacao` DISABLE KEYS */;
INSERT INTO `transacao` VALUES ('6710363e3da27','1','normal','921797626','947436662','app',7000.09,'urgente','07-09-2024','07','09','2024',1),('671039056e3cc','10','normal','947436662','921797626','app',500.00,'urgente','18-10-2024','18','10','2024',0),('6710363e3da27','2','normal','921797626','947436662','app',17000.00,'urgente','08-09-2024','08','09','2024',1),('6710363e3da27','3','normal','921797626','947436662','app',74000.00,'urgente','09-09-2024','09','09','2024',1),('6710363e3da27','4','normal','921797626','947436662','app',2000.00,'urgente','15-09-2024','15','09','2024',1),('6710363e3da27','5','normal','921797626','947436662','app',1500.00,'urgente','01-10-2024','01','10','2024',1),('6710363e3da27','6','parcelado','921797626','947436662','app',900.00,'urgente','01-10-2024','01','10','2024',1),('6710363e3da27','7','recorrente','921797626','947436662','app',70.00,'urgente','07-10-2024','07','10','2024',1),('6710363e3da27','8','normal','921797626','947436662','app',5625.00,'urgente','17-10-2024','17','19','2024',1),('6710363e3da27','9','normal','921797626','947436662','app',500.00,'urgente','18-10-2024','18','10','2024',0);
/*!40000 ALTER TABLE `transacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'fetafacil'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-21 16:06:00
