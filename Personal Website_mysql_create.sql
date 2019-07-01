CREATE TABLE `Primary` (
	`name` varchar(30) NOT NULL,
	`description` varchar(1000) NOT NULL,
	`projectYear` year(4) NOT NULL,
	`languageFK` tinyint(2) NOT NULL,
	`link` varchar(100) NOT NULL
);

CREATE TABLE `Languages` (
	`languagePK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`language` varchar(20) NOT NULL,
	PRIMARY KEY (`languagePK`)
);

ALTER TABLE `Primary` ADD CONSTRAINT `Primary_fk0` FOREIGN KEY (`languageFK`) REFERENCES `Languages`(`languagePK`);

