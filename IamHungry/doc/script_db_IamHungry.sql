SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `IamHungry` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `IamHungry` ;

-- -----------------------------------------------------
-- Table `IamHungry`.`recipe`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`recipe` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `recipe` TINYTEXT NULL ,
  `instructions` TEXT NOT NULL ,
  `nb_servings` INT NULL ,
  `preparation_time` INT NULL ,
  `id_category` INT NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`ingredient`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`ingredient` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TINYTEXT NULL ,
  `serving_unit` VARCHAR(45) NULL COMMENT 'e.g.  gramms, cups, lbs...' ,
  `id_category` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(60) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(512) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`ass_recipe_ingredient`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`ass_recipe_ingredient` (
  `id_recipe` INT NOT NULL ,
  `id_ingredient` INT NOT NULL ,
  `quantity` INT NULL ,
  PRIMARY KEY (`id_recipe`, `id_ingredient`) ,
  INDEX `fk_AssRecipeIngredient_Ingredient1_idx` (`id_ingredient` ASC) ,
  INDEX `fk_AssRecipeIngredient_Recipe1_idx` (`id_recipe` ASC) ,
  CONSTRAINT `fk_AssRecipeIngredient_Ingredient1`
    FOREIGN KEY (`id_ingredient` )
    REFERENCES `IamHungry`.`ingredient` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AssRecipeIngredient_Recipe1`
    FOREIGN KEY (`id_recipe` )
    REFERENCES `IamHungry`.`recipe` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`list`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`list` (
  `id_entity` INT NOT NULL COMMENT 'entities: recipe ingredients / grocery list / user in hand' ,
  `id_ingredient` INT NOT NULL ,
  `quantity` INT NULL ,
  `list_type` VARCHAR(10) NOT NULL COMMENT 'Type: recipe ingredients / grocery list / user in hand' ,
  PRIMARY KEY (`id_entity`, `id_ingredient`) ,
  INDEX `fk_List_Recipe1_idx` (`id_entity` ASC) ,
  INDEX `fk_List_Ingredient1_idx` (`id_ingredient` ASC) ,
  CONSTRAINT `fk_List_Recipe1`
    FOREIGN KEY (`id_entity` )
    REFERENCES `IamHungry`.`recipe` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_List_User1`
    FOREIGN KEY (`id_entity` )
    REFERENCES `IamHungry`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_List_Ingredient1`
    FOREIGN KEY (`id_ingredient` )
    REFERENCES `IamHungry`.`ingredient` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`grocery_list`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`grocery_list` (
  `id_user` INT NOT NULL ,
  `id_ingredient` INT NOT NULL ,
  `quantity` INT NULL ,
  PRIMARY KEY (`id_user`, `id_ingredient`) ,
  INDEX `fk_GroceryList_User1_idx` (`id_user` ASC) ,
  INDEX `fk_GroceryList_Ingredient1_idx` (`id_ingredient` ASC) ,
  CONSTRAINT `fk_GroceryList_User1`
    FOREIGN KEY (`id_user` )
    REFERENCES `IamHungry`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GroceryList_Ingredient1`
    FOREIGN KEY (`id_ingredient` )
    REFERENCES `IamHungry`.`ingredient` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`recipe-ingredient_category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`recipe-ingredient_category` (
  `id` INT NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TINYTEXT NULL ,
  `id_parent_category` INT NULL DEFAULT NULL ,
  `type` VARCHAR(10) NULL COMMENT 'Type: ingredient category / recipe category' ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_Category_Recipe1`
    FOREIGN KEY (`id` )
    REFERENCES `IamHungry`.`recipe` (`id_category` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Category_Ingredient1`
    FOREIGN KEY (`id` )
    REFERENCES `IamHungry`.`ingredient` (`id_category` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`user_inHand`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`user_inHand` (
  `id_user` INT NOT NULL ,
  `id_ingredient` INT NOT NULL ,
  `quantity` INT NULL ,
  PRIMARY KEY (`id_user`, `id_ingredient`) ,
  INDEX `fk_UserInHand_User1_idx` (`id_user` ASC) ,
  INDEX `fk_UserInHand_Ingredient1_idx` (`id_ingredient` ASC) ,
  CONSTRAINT `fk_UserInHand_User1`
    FOREIGN KEY (`id_user` )
    REFERENCES `IamHungry`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserInHand_Ingredient1`
    FOREIGN KEY (`id_ingredient` )
    REFERENCES `IamHungry`.`ingredient` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`week_meals`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`week_meals` (
  `id` INT NOT NULL ,
  `dayOfWeek_id` INT NOT NULL ,
  `dayOfWeek` VARCHAR(15) NOT NULL ,
  `mealOfDay_id` INT NOT NULL ,
  `mealOfDay` VARCHAR(15) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `IamHungry`.`ass_recipe_meal`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `IamHungry`.`ass_recipe_meal` (
  `id_user` INT NOT NULL ,
  `id_recipe` INT NOT NULL ,
  `id_meal` INT NOT NULL ,
  PRIMARY KEY (`id_recipe`, `id_user`, `id_meal`) ,
  INDEX `fk_AssRecipeWeek_User1_idx` (`id_user` ASC) ,
  INDEX `fk_AssRecipeWeek_Recipe1_idx` (`id_recipe` ASC) ,
  INDEX `fk_AssRecipeWeek_WeekMeals1_idx` (`id_meal` ASC) ,
  CONSTRAINT `fk_AssRecipeWeek_User1`
    FOREIGN KEY (`id_user` )
    REFERENCES `IamHungry`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AssRecipeWeek_Recipe1`
    FOREIGN KEY (`id_recipe` )
    REFERENCES `IamHungry`.`recipe` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AssRecipeWeek_WeekMeals1`
    FOREIGN KEY (`id_meal` )
    REFERENCES `IamHungry`.`week_meals` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `IamHungry` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
