CREATE TABLE `dbdev`.`tb_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `chatId` VARCHAR(100) NULL,
  `videoId` VARCHAR(45) NULL,
  `lastDateComment` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `dbdev`.`comment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comment` VARCHAR(355) NULL,
  `authorName` VARCHAR(45) NULL,
  `authorUrlProfile` VARCHAR(355) NULL,
  `idGoogleComment` VARCHAR(355) NULL,
  `dataComment` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `dbdev`.`tb_settings` 
DROP COLUMN `chatId`;

ALTER TABLE `dbdev`.`tb_settings` 
DROP COLUMN `videoId`;

ALTER TABLE `dbdev`.`tb_settings` 
CHANGE COLUMN `id` `chatId` VARCHAR(355) NOT NULL ;

ALTER TABLE `dbdev`.`tb_settings` 
RENAME TO  `dbdev`.`settings` ;

ALTER TABLE `dbdev`.`comment` 
ADD COLUMN `userSessionId` VARCHAR(355) NULL AFTER `dataComment`;





