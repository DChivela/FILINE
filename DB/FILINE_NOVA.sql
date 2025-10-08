-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16-Ago-2025 às 15:20
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `hospital`
--
CREATE DATABASE IF NOT EXISTS `hospital` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hospital`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_alergia`
--

CREATE TABLE `tb_alergia` (
  `Cod_Alergia` bigint(10) NOT NULL,
  `Tipo_Alergia` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_pre_triagem`
--

CREATE TABLE `tb_pre_triagem` (
  `Cod_Pre_Triagem` int(11) NOT NULL,
  `Nome_Paciente` varchar(200) NOT NULL,
  `Nome_Encarregado` varchar(200) DEFAULT NULL,
  `Genero_Paciente` enum('Masculino','Feminino','Outro') DEFAULT 'Masculino',
  `Data_Nascimento` date DEFAULT NULL,
  `Contacto` varchar(50) DEFAULT NULL,
  `Endereco` varchar(250) DEFAULT NULL,
  `Tipo_Ocorrencia` varchar(150) DEFAULT NULL,
  `Sintoma_Principal` varchar(250) DEFAULT NULL,
  `Classificacao_de_Risco` varchar(50) DEFAULT NULL,
  `Data_de_Registo` datetime DEFAULT current_timestamp(),
  `Tipo_Sangue` bigint(20) DEFAULT NULL,
  `Alergia` bigint(20) DEFAULT NULL,
  `Situacao` varchar(100) DEFAULT NULL,
  `Senha_de_Atendimento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_tipo_sangue`
--

CREATE TABLE `tb_tipo_sangue` (
  `Cod_Tipo_Sangue` bigint(10) NOT NULL,
  `Tipo_Sangue` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_triagem`
--

CREATE TABLE `tb_triagem` (
  `Cod_Triagem` bigint(10) NOT NULL,
  `Paciente` bigint(20) NOT NULL,
  `Peso` double DEFAULT NULL,
  `Altura` double DEFAULT NULL,
  `P/A` double DEFAULT NULL,
  `P/B` double DEFAULT NULL,
  `Edema` double DEFAULT NULL,
  `Pulso` double DEFAULT NULL,
  `SatO2` double DEFAULT NULL,
  `P_Respiratorio` double DEFAULT NULL,
  `Dor` varchar(45) DEFAULT NULL,
  `Temperatura` double DEFAULT NULL,
  `Data_Triagem` datetime(6) DEFAULT NULL,
  `tb_Utilizador_Cod_Utilizador` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_utilizador`
--

CREATE TABLE `tb_utilizador` (
  `Cod_Utilizador` bigint(10) NOT NULL,
  `Nome_Enfermeiro` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Contacto` varchar(45) DEFAULT NULL,
  `Senha` varchar(45) DEFAULT NULL,
  `Perfil_Acesso` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tb_alergia`
--
ALTER TABLE `tb_alergia`
  ADD PRIMARY KEY (`Cod_Alergia`);

--
-- Índices para tabela `tb_pre_triagem`
--
ALTER TABLE `tb_pre_triagem`
  ADD PRIMARY KEY (`Cod_Pre_Triagem`),
  ADD KEY `Tipo_Sangue` (`Tipo_Sangue`),
  ADD KEY `Alergia` (`Alergia`);

--
-- Índices para tabela `tb_tipo_sangue`
--
ALTER TABLE `tb_tipo_sangue`
  ADD PRIMARY KEY (`Cod_Tipo_Sangue`);

--
-- Índices para tabela `tb_triagem`
--
ALTER TABLE `tb_triagem`
  ADD PRIMARY KEY (`Cod_Triagem`),
  ADD KEY `tb_Utilizador_Cod_Utilizador` (`tb_Utilizador_Cod_Utilizador`);

--
-- Índices para tabela `tb_utilizador`
--
ALTER TABLE `tb_utilizador`
  ADD PRIMARY KEY (`Cod_Utilizador`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_pre_triagem`
--
ALTER TABLE `tb_pre_triagem`
  MODIFY `Cod_Pre_Triagem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_triagem`
--
ALTER TABLE `tb_triagem`
  MODIFY `Cod_Triagem` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tb_pre_triagem`
--
ALTER TABLE `tb_pre_triagem`
  ADD CONSTRAINT `tb_pre_triagem_ibfk_1` FOREIGN KEY (`Tipo_Sangue`) REFERENCES `tb_tipo_sangue` (`Cod_Tipo_Sangue`),
  ADD CONSTRAINT `tb_pre_triagem_ibfk_2` FOREIGN KEY (`Alergia`) REFERENCES `tb_alergia` (`Cod_Alergia`);

--
-- Limitadores para a tabela `tb_triagem`
--
ALTER TABLE `tb_triagem`
  ADD CONSTRAINT `tb_triagem_ibfk_1` FOREIGN KEY (`tb_Utilizador_Cod_Utilizador`) REFERENCES `tb_utilizador` (`Cod_Utilizador`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
