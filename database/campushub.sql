-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema campushub
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema campushub
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `campushub` DEFAULT CHARACTER SET utf8mb3 ;
USE `campushub` ;

-- -----------------------------------------------------
-- Table `campushub`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`users` (
                                                   `user_id` INT NOT NULL AUTO_INCREMENT,
                                                   `first_name` VARCHAR(75) NOT NULL,
    `last_name` VARCHAR(75) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `gender` ENUM('male', 'female') NULL DEFAULT NULL,
    `email` VARCHAR(50) NULL DEFAULT NULL,
    `phone` VARCHAR(13) NOT NULL,
    `role` ENUM('student', 'teacher') NOT NULL DEFAULT 'student',
    `profile_img` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE INDEX `phone_UNIQUE` (`phone` ASC) VISIBLE,
    UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
    ENGINE = InnoDB
    AUTO_INCREMENT = 3
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`admin`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`admin` (
                                                   `admin_id` INT NOT NULL AUTO_INCREMENT,
                                                   `users_user_id` INT NOT NULL,
                                                   `role` ENUM('moderator', 'admin', 'super_admin') NOT NULL,
    PRIMARY KEY (`admin_id`),
    UNIQUE INDEX `users_user_id_UNIQUE` (`users_user_id` ASC) VISIBLE,
    INDEX `fk_admin_users_idx` (`users_user_id` ASC) VISIBLE,
    CONSTRAINT `fk_admin_users`
    FOREIGN KEY (`users_user_id`)
    REFERENCES `campushub`.`users` (`user_id`))
    ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`announcements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`announcements` (
                                                           `id` INT NOT NULL AUTO_INCREMENT,
                                                           `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'General',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `content` TEXT NOT NULL,
    `date_posted` DATETIME NOT NULL,
    `date_expire` DATETIME NOT NULL,
    `admin_admin_id` INT NOT NULL,
    `meta_data` JSON NULL DEFAULT NULL,
    `status` ENUM('display', 'hide') NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_announcements_admin1_idx` (`admin_admin_id` ASC) VISIBLE,
    CONSTRAINT `fk_announcements_admin1`
    FOREIGN KEY (`admin_admin_id`)
    REFERENCES `campushub`.`admin` (`admin_id`))
    ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`events`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`events` (
                                                    `event_id` INT NOT NULL AUTO_INCREMENT,
                                                    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATE NOT NULL,
    `event_start_time` TIME NOT NULL,
    `event_end_time` TIME NOT NULL,
    `img_path` VARCHAR(255) NOT NULL,
    `meta_data` JSON NULL DEFAULT NULL,
    `status` ENUM('display', 'hide') NOT NULL,
    `seats` INT NOT NULL,
    PRIMARY KEY (`event_id`))
    ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`master_control`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`master_control` (
                                                            `id` INT NOT NULL AUTO_INCREMENT,
                                                            `email` VARCHAR(75) NULL DEFAULT NULL,
    PRIMARY KEY (`id`))
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`media` (
                                                   `media_id` INT NOT NULL AUTO_INCREMENT,
                                                   `title` VARCHAR(150) NOT NULL,
    `media_type` ENUM('image', 'video') NOT NULL,
    `meta_data` JSON NULL DEFAULT NULL,
    `events_event_id` INT NOT NULL,
    `users_user_id` INT NOT NULL,
    `thumbnail_path` VARCHAR(255) NULL DEFAULT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`media_id`),
    INDEX `fk_media_events1_idx` (`events_event_id` ASC) VISIBLE,
    INDEX `fk_media_users1_idx` (`users_user_id` ASC) VISIBLE,
    CONSTRAINT `fk_media_events1`
    FOREIGN KEY (`events_event_id`)
    REFERENCES `campushub`.`events` (`event_id`),
    CONSTRAINT `fk_media_users1`
    FOREIGN KEY (`users_user_id`)
    REFERENCES `campushub`.`users` (`user_id`))
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `campushub`.`registrations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `campushub`.`registrations` (
                                                           `registration_id` INT NOT NULL AUTO_INCREMENT,
                                                           `reg_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                                           `events_event_id` INT NOT NULL,
                                                           `users_user_id` INT NOT NULL,
                                                           PRIMARY KEY (`registration_id`),
    INDEX `fk_registrations_events1_idx` (`events_event_id` ASC) VISIBLE,
    INDEX `fk_registrations_users1_idx` (`users_user_id` ASC) VISIBLE,
    CONSTRAINT `fk_registrations_events1`
    FOREIGN KEY (`events_event_id`)
    REFERENCES `campushub`.`events` (`event_id`),
    CONSTRAINT `fk_registrations_users1`
    FOREIGN KEY (`users_user_id`)
    REFERENCES `campushub`.`users` (`user_id`))
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
