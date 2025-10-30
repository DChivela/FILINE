-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Out-2025 às 17:18
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
-- Banco de dados: `filine`
--
CREATE DATABASE IF NOT EXISTS `filine` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `filine`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `Endereco` bigint(10) NOT NULL,
  `morada` varchar(255) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `enderecos`
--

INSERT INTO `enderecos` (`Endereco`, `morada`, `rua`) VALUES
(1, 'Bairro Comercial', 'Rua 4 de Fevereiro'),
(2, 'Bairro Lucrêcia', 'Rua 1º de Maio'),
(3, 'Lubango, Bairro da Mapunda', 'Rua de Baixo'),
(4, 'CRISTO REI', ''),
(5, 'Bairro Comercial', 'Rua Deolinda Rodrigues 14');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_alergia`
--

CREATE TABLE `tb_alergia` (
  `Cod_Alergia` bigint(10) NOT NULL,
  `Tipo_Alergia` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tb_alergia`
--

INSERT INTO `tb_alergia` (`Cod_Alergia`, `Tipo_Alergia`) VALUES
(1, 'Renite'),
(2, 'Alergia respiratória'),
(4, 'Alimentar'),
(5, 'Asma Alérgica'),
(6, 'Alergia a picadas de insetos'),
(7, 'Nenhuma');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_pre_triagem`
--

CREATE TABLE `tb_pre_triagem` (
  `Cod_Pre_Triagem` int(11) NOT NULL,
  `Nome_Paciente` varchar(200) NOT NULL,
  `Grupo_Ocorrencia` varchar(200) DEFAULT NULL,
  `Genero_Paciente` enum('Masculino','Feminino','Outro') DEFAULT 'Masculino',
  `Data_Nascimento` date DEFAULT NULL,
  `Contacto` varchar(50) DEFAULT NULL,
  `Tipo_Ocorrencia` varchar(150) DEFAULT NULL,
  `Sintoma_Principal` varchar(250) DEFAULT NULL,
  `Classificacao_de_Risco` varchar(50) DEFAULT NULL,
  `Motivos_Classificacao` varchar(255) NOT NULL,
  `Data_de_Registo` datetime DEFAULT current_timestamp(),
  `Tipo_Sangue` bigint(10) DEFAULT NULL,
  `Alergia` bigint(10) DEFAULT NULL,
  `Endereco` bigint(10) DEFAULT NULL,
  `Estado` enum('Pendente','Em Andamento','Atendido','') DEFAULT 'Pendente',
  `Situacao` enum('Em Espera','Em Andamento','Atendido','') DEFAULT 'Em Espera',
  `Senha_de_Atendimento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tb_pre_triagem`
--

INSERT INTO `tb_pre_triagem` (`Cod_Pre_Triagem`, `Nome_Paciente`, `Grupo_Ocorrencia`, `Genero_Paciente`, `Data_Nascimento`, `Contacto`, `Tipo_Ocorrencia`, `Sintoma_Principal`, `Classificacao_de_Risco`, `Motivos_Classificacao`, `Data_de_Registo`, `Tipo_Sangue`, `Alergia`, `Endereco`, `Estado`, `Situacao`, `Senha_de_Atendimento`) VALUES
(26, 'António Maurício', 'AGRESSAO', 'Masculino', '2000-10-17', '94584221', NULL, '', 'VERMELHO', 'choque', '2025-10-17 11:02:27', 1, 1, NULL, 'Pendente', 'Atendido', '20251017001'),
(33, 'Inês Vicente', 'ALERGIA', 'Feminino', '2000-10-17', '924084221', NULL, '', 'VERMELHO', 'choque', '2025-10-17 11:05:55', 1, 1, 1, 'Pendente', 'Em Espera', '20251017002'),
(34, 'AAAAAAAAAAA Vicente', 'AGRESSAO', 'Masculino', '2007-10-28', '94584221', NULL, '', 'AMARELO', 'dor_moderada', '2025-10-28 17:32:03', 5, 2, 3, 'Pendente', 'Atendido', '20251028001'),
(43, 'Diandre Mateus', 'DOR_CABECA', 'Masculino', '1989-10-29', '945842210', NULL, 'Tonturas', 'LARANJA', 'laranja', '2025-10-29 18:15:16', 2, 6, 1, 'Pendente', 'Em Espera', '20251029001'),
(46, 'Regina Mateus', 'DOR_CABECA', 'Feminino', '1985-10-21', '945842210', NULL, 'Tonturas', 'AMARELO', 'amarelo', '2025-10-29 18:40:26', 3, 7, 1, 'Pendente', 'Em Espera', '20251029002'),
(47, 'Regina Mateus', 'DOR_CERVICAL', 'Feminino', '2025-10-29', '945842210', NULL, 'Tonturas', 'VERMELHO', 'vermelho', '2025-10-29 18:42:35', 2, 7, 1, 'Pendente', 'Em Espera', '20251029003'),
(48, 'Dionísia Mande', 'NEUROLOGICO', 'Feminino', '1999-04-24', '94584221', NULL, '', 'VERMELHO', 'choque', '2025-10-30 15:57:01', 3, 2, 5, 'Pendente', 'Atendido', '20251030001');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_tipo_sangue`
--

CREATE TABLE `tb_tipo_sangue` (
  `Cod_Tipo_Sangue` bigint(10) NOT NULL,
  `Tipo_Sangue` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tb_tipo_sangue`
--

INSERT INTO `tb_tipo_sangue` (`Cod_Tipo_Sangue`, `Tipo_Sangue`) VALUES
(1, '+A'),
(2, '+B'),
(3, '+O'),
(5, '- A'),
(6, 'Indefinido');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_triagem`
--

CREATE TABLE `tb_triagem` (
  `Cod_Triagem` bigint(10) NOT NULL,
  `Paciente` bigint(20) NOT NULL,
  `Peso` double DEFAULT NULL,
  `Altura` double DEFAULT NULL,
  `PA` double DEFAULT NULL,
  `PB` double DEFAULT NULL,
  `Edema` double DEFAULT NULL,
  `Pulso` double DEFAULT NULL,
  `SatO2` double DEFAULT NULL,
  `P_Respiratorio` double DEFAULT NULL,
  `Dor` varchar(45) DEFAULT NULL,
  `Temperatura` double DEFAULT NULL,
  `Data_Triagem` datetime(6) DEFAULT current_timestamp(6),
  `tb_Utilizador_Cod_Utilizador` bigint(10) NOT NULL,
  `Cod_Pre_Triagem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tb_triagem`
--

INSERT INTO `tb_triagem` (`Cod_Triagem`, `Paciente`, `Peso`, `Altura`, `PA`, `PB`, `Edema`, `Pulso`, `SatO2`, `P_Respiratorio`, `Dor`, `Temperatura`, `Data_Triagem`, `tb_Utilizador_Cod_Utilizador`, `Cod_Pre_Triagem`) VALUES
(1, 0, 59, 169, 0, 0, 0, 0, 0, 0, 'Física', 36, '2025-10-29 12:04:50.000000', 1, 33),
(2, 0, 69, 170, 20, 10, 0, 32, 0, 0, 'Física', 36, '2025-10-30 16:14:55.000000', 1, 26),
(3, 0, 69, 170, 20, 10, 0, 32, 0, 0, 'Física', 36, '2025-10-30 16:18:24.000000', 1, 48);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_utilizador`
--

CREATE TABLE `tb_utilizador` (
  `Cod_Utilizador` bigint(10) NOT NULL,
  `Nome_Enfermeiro` varchar(255) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Contacto` varchar(45) DEFAULT NULL,
  `Senha` varchar(255) DEFAULT NULL,
  `Perfil_Acesso` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tb_utilizador`
--

INSERT INTO `tb_utilizador` (`Cod_Utilizador`, `Nome_Enfermeiro`, `Email`, `Contacto`, `Senha`, `Perfil_Acesso`) VALUES
(1, 'Administrador', 'admin@gmail.com', '244 947-449-844', '$2y$10$DjW3E.oPLuxlU3Ld/d.z8.1oi7MTyJf6.QGnMoikidRhX2V5RjPPq', 'admin'),
(2, 'Domingos Chivela', 'domingos.chivela@filine.com', '244 947-449-844', '$2y$10$DjW3E.oPLuxlU3Ld/d.z8.1oi7MTyJf6.QGnMoikidRhX2V5RjPPq', 'admin');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`Endereco`);

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
  ADD KEY `Alergia` (`Alergia`),
  ADD KEY `Endereco` (`Endereco`);

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
  ADD KEY `tb_Utilizador_Cod_Utilizador` (`tb_Utilizador_Cod_Utilizador`),
  ADD KEY `FK_Pre_Triagem` (`Cod_Pre_Triagem`);

--
-- Índices para tabela `tb_utilizador`
--
ALTER TABLE `tb_utilizador`
  ADD PRIMARY KEY (`Cod_Utilizador`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `Endereco` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_alergia`
--
ALTER TABLE `tb_alergia`
  MODIFY `Cod_Alergia` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_pre_triagem`
--
ALTER TABLE `tb_pre_triagem`
  MODIFY `Cod_Pre_Triagem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de tabela `tb_tipo_sangue`
--
ALTER TABLE `tb_tipo_sangue`
  MODIFY `Cod_Tipo_Sangue` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `tb_triagem`
--
ALTER TABLE `tb_triagem`
  MODIFY `Cod_Triagem` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tb_utilizador`
--
ALTER TABLE `tb_utilizador`
  MODIFY `Cod_Utilizador` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `tb_pre_triagem`
--
ALTER TABLE `tb_pre_triagem`
  ADD CONSTRAINT `tb_pre_triagem_ibfk_1` FOREIGN KEY (`Tipo_Sangue`) REFERENCES `tb_tipo_sangue` (`Cod_Tipo_Sangue`),
  ADD CONSTRAINT `tb_pre_triagem_ibfk_2` FOREIGN KEY (`Alergia`) REFERENCES `tb_alergia` (`Cod_Alergia`),
  ADD CONSTRAINT `tb_pre_triagem_ibfk_3` FOREIGN KEY (`Endereco`) REFERENCES `enderecos` (`Endereco`);

--
-- Limitadores para a tabela `tb_triagem`
--
ALTER TABLE `tb_triagem`
  ADD CONSTRAINT `FK_Pre_Triagem` FOREIGN KEY (`Cod_Pre_Triagem`) REFERENCES `tb_pre_triagem` (`Cod_Pre_Triagem`),
  ADD CONSTRAINT `tb_triagem_ibfk_1` FOREIGN KEY (`tb_Utilizador_Cod_Utilizador`) REFERENCES `tb_utilizador` (`Cod_Utilizador`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
