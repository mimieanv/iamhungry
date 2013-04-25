-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 25, 2013 at 06:18 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `iamhungry`
--

-- --------------------------------------------------------

--
-- Table structure for table `ass_recipe_ingredient`
--

CREATE TABLE IF NOT EXISTS `ass_recipe_ingredient` (
  `id_recipe` int(11) NOT NULL,
  `id_ingredient` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_recipe`,`id_ingredient`),
  KEY `fk_AssRecipeIngredient_Ingredient1_idx` (`id_ingredient`),
  KEY `fk_AssRecipeIngredient_Recipe1_idx` (`id_recipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ass_recipe_ingredient`
--

INSERT INTO `ass_recipe_ingredient` (`id_recipe`, `id_ingredient`, `quantity`) VALUES
(1, 1, 25),
(1, 2, 50),
(2, 2, 200),
(2, 4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `ass_recipe_meal`
--

CREATE TABLE IF NOT EXISTS `ass_recipe_meal` (
  `id_user` int(11) NOT NULL,
  `id_recipe` int(11) NOT NULL,
  `id_meal` int(11) NOT NULL,
  PRIMARY KEY (`id_recipe`,`id_user`,`id_meal`),
  KEY `fk_AssRecipeWeek_User1_idx` (`id_user`),
  KEY `fk_AssRecipeWeek_Recipe1_idx` (`id_recipe`),
  KEY `fk_AssRecipeWeek_WeekMeals1_idx` (`id_meal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ass_recipe_meal`
--

INSERT INTO `ass_recipe_meal` (`id_user`, `id_recipe`, `id_meal`) VALUES
(2, 1, 2),
(2, 1, 5),
(2, 1, 6),
(2, 1, 9),
(2, 1, 14),
(2, 1, 15),
(2, 2, 1),
(2, 2, 14);

-- --------------------------------------------------------

--
-- Table structure for table `grocery_list`
--

CREATE TABLE IF NOT EXISTS `grocery_list` (
  `id_user` int(11) NOT NULL,
  `id_ingredient` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_user`,`id_ingredient`),
  KEY `fk_GroceryList_User1_idx` (`id_user`),
  KEY `fk_GroceryList_Ingredient1_idx` (`id_ingredient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `grocery_list`
--

INSERT INTO `grocery_list` (`id_user`, `id_ingredient`, `quantity`) VALUES
(2, 2, 155),
(2, 4, 18);

-- --------------------------------------------------------

--
-- Table structure for table `ingredient`
--

CREATE TABLE IF NOT EXISTS `ingredient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` tinytext,
  `serving_unit` varchar(45) DEFAULT NULL COMMENT 'e.g.  gramms, cups, lbs...',
  `id_category` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ingredient`
--

INSERT INTO `ingredient` (`id`, `name`, `description`, `serving_unit`, `id_category`) VALUES
(1, 'Penne', 'Pasta', 'gramms', 1),
(2, 'Elbows', 'weird pastas', 'gramms', 1),
(4, 'Salad', 'rabbits love it', 'leafs', 4),
(5, 'Corn', 'yellow por favor', 'cup', 4);

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE IF NOT EXISTS `recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` tinytext,
  `instructions` text NOT NULL,
  `nb_servings` int(11) DEFAULT NULL,
  `preparation_time` int(11) DEFAULT NULL,
  `id_category` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`id`, `name`, `description`, `instructions`, `nb_servings`, `preparation_time`, `id_category`) VALUES
(1, 'Penne and elbows', 'A yummy mix of penne and elbows!', 'Boil water. Put penne and elbows in it. Wait. Eat.', 1, 15, 2),
(2, 'Green elbows', 'Green pasta recipe', 'Pasta in Wata(r). Salad in pasta. The end.', 1, 30, 2);

-- --------------------------------------------------------

--
-- Table structure for table `recipe-ingredient_category`
--

CREATE TABLE IF NOT EXISTS `recipe-ingredient_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `description` tinytext,
  `id_parent_category` int(11) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL COMMENT 'Type: ingredient category / recipe category',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `recipe-ingredient_category`
--

INSERT INTO `recipe-ingredient_category` (`id`, `name`, `description`, `id_parent_category`, `type`) VALUES
(1, 'Pasta', 'Good pastas', NULL, 'ingredient'),
(2, 'Pasta Mix', 'A lot of different pastas, yum!', NULL, 'recipe'),
(3, 'breakfast', 'Good morning sir!', NULL, 'recipe'),
(4, 'Green vegie', 'Vegie', NULL, 'ingredient');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `name` varchar(45) NOT NULL,
  `password` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `name`, `password`) VALUES
(2, 'gros@con.org', 'Gros con org', '4d58745f49dcd2321117e553d1b619df4559e1b44e4195810fe22caa43c2a9bea7d5b3df8dd9fb1e17d5e7577ad968fb2f7d22744c7b6424bfa5888224627d0a');

-- --------------------------------------------------------

--
-- Table structure for table `user_inhand`
--

CREATE TABLE IF NOT EXISTS `user_inhand` (
  `id_user` int(11) NOT NULL,
  `id_ingredient` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_user`,`id_ingredient`),
  KEY `fk_UserInHand_User1_idx` (`id_user`),
  KEY `fk_UserInHand_Ingredient1_idx` (`id_ingredient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_inhand`
--

INSERT INTO `user_inhand` (`id_user`, `id_ingredient`, `quantity`) VALUES
(2, 1, 300),
(2, 2, 545),
(2, 4, 2),
(2, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `week_meals`
--

CREATE TABLE IF NOT EXISTS `week_meals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dayOfWeek_id` int(11) NOT NULL,
  `dayOfWeek` varchar(15) NOT NULL,
  `mealOfDay_id` int(11) NOT NULL,
  `mealOfDay` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `week_meals`
--

INSERT INTO `week_meals` (`id`, `dayOfWeek_id`, `dayOfWeek`, `mealOfDay_id`, `mealOfDay`) VALUES
(1, 1, 'Monday', 2, 'Lunch'),
(2, 1, 'Monday', 3, 'Dinner'),
(3, 2, 'Tuesday', 2, 'Lunch'),
(4, 2, 'Tuesday', 3, 'Dinner'),
(5, 3, 'Wednesday', 2, 'Lunch'),
(6, 3, 'Wednesday', 3, 'Dinner'),
(7, 4, 'Thurdsay', 2, 'Lunch'),
(8, 4, 'Thurdsay', 3, 'Dinner'),
(9, 5, 'Friday', 2, 'Lunch'),
(10, 5, 'Friday', 3, 'Dinner'),
(11, 6, 'Saturday', 2, 'Lunch'),
(12, 6, 'Saturday', 3, 'Dinner'),
(13, 7, 'Sunday', 1, 'Breakfast'),
(14, 7, 'Sunday', 2, 'Lunch'),
(15, 7, 'Sunday', 3, 'Dinner');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ass_recipe_ingredient`
--
ALTER TABLE `ass_recipe_ingredient`
  ADD CONSTRAINT `fk_AssRecipeIngredient_Ingredient1` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_AssRecipeIngredient_Recipe1` FOREIGN KEY (`id_recipe`) REFERENCES `recipe` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ass_recipe_meal`
--
ALTER TABLE `ass_recipe_meal`
  ADD CONSTRAINT `fk_AssRecipeWeek_Recipe1` FOREIGN KEY (`id_recipe`) REFERENCES `recipe` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_AssRecipeWeek_User1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_AssRecipeWeek_WeekMeals1` FOREIGN KEY (`id_meal`) REFERENCES `week_meals` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `grocery_list`
--
ALTER TABLE `grocery_list`
  ADD CONSTRAINT `fk_GroceryList_Ingredient1` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_GroceryList_User1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_inhand`
--
ALTER TABLE `user_inhand`
  ADD CONSTRAINT `fk_UserInHand_Ingredient1` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredient` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_UserInHand_User1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
