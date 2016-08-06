SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Cathegories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Cathegories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Name` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
PACK_KEYS = DEFAULT;


-- -----------------------------------------------------
-- Table `mydb`.`Founds`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Founds` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Num` INT NOT NULL,
  `Name` LONGTEXT NULL,
  `Type` INT NOT NULL,
  `Cathegory` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `Cat_to_founds_idx` (`Cathegory` ASC),
  CONSTRAINT `Cat_to_founds`
    FOREIGN KEY (`Cathegory`)
    REFERENCES `mydb`.`Cathegories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Registers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Registers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Num` INT NOT NULL,
  `Found` INT NOT NULL,
  `Name` LONGTEXT NULL,
  `Annot` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `Found_to_Register_idx` (`Found` ASC),
  CONSTRAINT `Found_to_Register`
    FOREIGN KEY (`Found`)
    REFERENCES `mydb`.`Founds` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Deals`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Deals` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Num` INT NOT NULL,
  `Name` LONGTEXT NULL,
  `Found` INT NOT NULL,
  `Register` INT NOT NULL,
  `Annot` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `Found_to_deal_idx` (`Found` ASC),
  INDEX `Found_to_register_idx` (`Register` ASC),
  CONSTRAINT `Found_to_deal`
    FOREIGN KEY (`Found`)
    REFERENCES `mydb`.`Founds` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Register_to_deal`
    FOREIGN KEY (`Register`)
    REFERENCES `mydb`.`Registers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Lists`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Lists` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Num` INT NOT NULL,
  `Name` LONGTEXT NULL,
  `Found` INT NOT NULL,
  `Register` INT NOT NULL,
  `Deal` INT NOT NULL,
  `Path` LONGTEXT NOT NULL,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  PRIMARY KEY (`id`),
  INDEX `Found_to_list_idx` (`Found` ASC),
  INDEX `Register_to_list_idx` (`Register` ASC),
  INDEX `Deal_to_list_idx` (`Deal` ASC),
  CONSTRAINT `Found_to_list`
    FOREIGN KEY (`Found`)
    REFERENCES `mydb`.`Founds` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Register_to_list`
    FOREIGN KEY (`Register`)
    REFERENCES `mydb`.`Registers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Deal_to_list`
    FOREIGN KEY (`Deal`)
    REFERENCES `mydb`.`Deals` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(40) NOT NULL,
  `Password` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Actions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Actions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Name` LONGTEXT NULL,
  `Action` TEXT NULL,
  `SQLQUERY` LONGTEXT NULL,
  `IP` TEXT NULL,
  `DATE` DATE NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
