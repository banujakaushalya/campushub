-- MySQL Workbench Synchronization
-- Generated: 2026-08-25 18:45
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: KATANA

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

ALTER SCHEMA `campushub`  DEFAULT CHARACTER SET utf8  DEFAULT COLLATE utf8_general_ci ;

ALTER TABLE `campushub`.`admin` 
DROP FOREIGN KEY `fk_admin_users`;

ALTER TABLE `campushub`.`registrations` 
DROP FOREIGN KEY `fk_registrations_events1`,
DROP FOREIGN KEY `fk_registrations_users1`;

ALTER TABLE `campushub`.`announcements` 
DROP FOREIGN KEY `fk_announcements_admin1`;

ALTER TABLE `campushub`.`media` 
DROP FOREIGN KEY `fk_media_events1`,
DROP FOREIGN KEY `fk_media_users1`;

ALTER TABLE `campushub`.`users` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ,
CHANGE COLUMN `last_name` `last_name` VARCHAR(75) NOT NULL ;

ALTER TABLE `campushub`.`admin` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ;

ALTER TABLE `campushub`.`events` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ,
CHANGE COLUMN `meta_data` `meta_data` JSON NULL ;

ALTER TABLE `campushub`.`registrations` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ;

ALTER TABLE `campushub`.`announcements` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ,
CHANGE COLUMN `meta_data` `meta_data` JSON NULL ;

ALTER TABLE `campushub`.`media` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ,
CHANGE COLUMN `meta_data` `meta_data` JSON NULL ;

ALTER TABLE `campushub`.`master_control` 
CHARACTER SET = utf8 , COLLATE = utf8_general_ci ;

ALTER TABLE `campushub`.`admin` 
ADD CONSTRAINT `fk_admin_users`
  FOREIGN KEY (`users_user_id`)
  REFERENCES `campushub`.`users` (`user_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `campushub`.`registrations` 
ADD CONSTRAINT `fk_registrations_events1`
  FOREIGN KEY (`events_event_id`)
  REFERENCES `campushub`.`events` (`event_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_registrations_users1`
  FOREIGN KEY (`users_user_id`)
  REFERENCES `campushub`.`users` (`user_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `campushub`.`announcements` 
ADD CONSTRAINT `fk_announcements_admin1`
  FOREIGN KEY (`admin_admin_id`)
  REFERENCES `campushub`.`admin` (`admin_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `campushub`.`media` 
ADD CONSTRAINT `fk_media_events1`
  FOREIGN KEY (`events_event_id`)
  REFERENCES `campushub`.`events` (`event_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_media_users1`
  FOREIGN KEY (`users_user_id`)
  REFERENCES `campushub`.`users` (`user_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
