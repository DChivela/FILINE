-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `hospital` ;

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `hospital` DEFAULT CHARACTER SET utf8 ;
SHOW WARNINGS;
-- -----------------------------------------------------
-- Schema db_filine
-- -----------------------------------------------------
SHOW WARNINGS;
USE `hospital` ;

-- -----------------------------------------------------
-- Table `tb_Tipo_Sangue`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tb_Tipo_Sangue` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tb_Tipo_Sangue` (
  `Cod_Tipo_Sangue` BIGINT(10) NOT NULL,
  `Tipo_Sangue` VARCHAR(45) NULL,
  PRIMARY KEY (`Cod_Tipo_Sangue`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tb_Alergia`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tb_Alergia` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tb_Alergia` (
  `Cod_Alergia` BIGINT(10) NOT NULL,
  `Tipo_Alergia` VARCHAR(45) NULL,
  PRIMARY KEY (`Cod_Alergia`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `Tb_Pre_Triagem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Tb_Pre_Triagem` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `Tb_Pre_Triagem` (
  `Cod_Pre_Triagem` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `Nome_Paciente` VARCHAR(45) NOT NULL,
  `Nome_Encarregado` VARCHAR(45) NULL,
  `Genero_Paciente` VARCHAR(45) NOT NULL,
  `Data_Nascimento` DATE NULL,
  `Contacto` VARCHAR(45) NULL,
  `Endereco` VARCHAR(45) NULL,
  `Tipo_Ocorrencia` VARCHAR(45) NOT NULL,
  `Sintoma_Principal` VARCHAR(45) NOT NULL,
  `Classificacao_de_Risco` VARCHAR(45) NULL,
  `Data_de_Registo` DATETIME(6) NULL,
  `Tipo_Sangue` BIGINT(10) NOT NULL,
  `Alergia` BIGINT(10) NOT NULL,
  `Situacao` VARCHAR(45) NULL,
  `Senha_de_Atendimento` VARCHAR(45) NULL,
  PRIMARY KEY (`Cod_Pre_Triagem`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tb_Utilizador`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tb_Utilizador` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tb_Utilizador` (
  `Cod_Utilizador` BIGINT(10) NOT NULL,
  `Nome_Enfermeiro` VARCHAR(45) NULL,
  `Email` VARCHAR(45) NULL,
  `Contacto` VARCHAR(45) NULL,
  `Senha` VARCHAR(45) NULL,
  `Perfil_Acesso` VARCHAR(45) NULL,
  PRIMARY KEY (`Cod_Utilizador`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `tb_Triagem`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tb_Triagem` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `tb_Triagem` (
  `Cod_Triagem` BIGINT(10) NOT NULL AUTO_INCREMENT,
  `Paciente` BIGINT(20) NOT NULL,
  `Peso` DOUBLE NULL,
  `Altura` DOUBLE NULL,
  `P/A` DOUBLE NULL,
  `P/B` DOUBLE NULL,
  `Edema` DOUBLE NULL,
  `Pulso` DOUBLE NULL,
  `SatO2` DOUBLE NULL,
  `P_Respiratorio` DOUBLE NULL,
  `Dor` VARCHAR(45) NULL,
  `Temperatura` DOUBLE NULL,
  `Data_Triagem` DATETIME(6) NULL,
  `tb_Utilizador_Cod_Utilizador` BIGINT(10) NOT NULL,
  PRIMARY KEY (`Cod_Triagem`))
ENGINE = InnoDB;

SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
