CREATE TABLE `Projects` (
	`uniqueKey` tinyint(2) NOT NULL UNIQUE,
	`name` varchar(30) NOT NULL,
	`projectYearFK` tinyint(2) NOT NULL,
	`Link` varchar(100) NOT NULL,
	`projectDescription` varchar(255) NOT NULL,
	`languageOneFK` tinyint(2) NOT NULL,
	`languageTwoFK` tinyint(2) NOT NULL,
	`languageThreeFK` tinyint(2) NOT NULL,
	`languageFourFk` tinyint(2) NOT NULL,
	`languageFiveFK` tinyint(2) NOT NULL
);

CREATE TABLE `Languages` (
	`languagePK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`language` varchar(20) NOT NULL,
	PRIMARY KEY (`languagePK`)
);

CREATE TABLE `Education` (
	`uniqueKey` tinyint NOT NULL UNIQUE,
	`institutionFK` tinyint(2) NOT NULL UNIQUE,
	`subjectFK` tinyint(2) NOT NULL UNIQUE,
	`gradeFk` tinyint(2) NOT NULL UNIQUE,
	`subjectLevelFK` tinyint(2) NOT NULL,
	`Credits` tinyint NOT NULL,
	`ClassYearFK` tinyint(2) NOT NULL,
	`subjectAbbreviationFK` tinyint(10) NOT NULL DEFAULT '2',
	`Endorsement` varchar(1)
);

CREATE TABLE `Experience` (
	`uniqueKey` tinyint(2) NOT NULL UNIQUE,
	`name` varchar(30) NOT NULL,
	`experienceYearFK` tinyint(2) NOT NULL,
	`experienceDescription` varchar(255) NOT NULL,
	`languageOneFK` tinyint(2) NOT NULL,
	`languageTwoFK` tinyint(2) NOT NULL,
	`languageThreeFK` tinyint(2) NOT NULL,
	`languageFourFK` tinyint(2) NOT NULL,
	`languageFiveFK` tinyint(2) NOT NULL,
	`Link` varchar(100) NOT NULL
);

CREATE TABLE `Institution` (
	`institutionPK` tinyint(2) NOT NULL,
	`institution` varchar(20) NOT NULL,
	PRIMARY KEY (`institutionPK`)
);

CREATE TABLE `Subject` (
	`subjectPK` tinyint(2) NOT NULL,
	`Subject` varchar(100) NOT NULL,
	PRIMARY KEY (`subjectPK`)
);

CREATE TABLE `Grade` (
	`gradePK` tinyint(2) NOT NULL,
	`Grade` varchar(1) NOT NULL,
	PRIMARY KEY (`gradePK`)
);

CREATE TABLE `subjectLevel` (
	`subjectLevelPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`subjectLevel` varchar(20) NOT NULL,
	PRIMARY KEY (`subjectLevelPK`)
);

CREATE TABLE `Year` (
	`yearPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`year` DATE(2) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`yearPK`)
);

CREATE TABLE `subjectAbbreviation` (
	`subjectAbbreviationPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`subjectAbbreviation` varchar(10) NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`subjectAbbreviationPK`)
);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk0` FOREIGN KEY (`projectYearFK`) REFERENCES `Year`(`yearPK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk4` FOREIGN KEY (`languageFourFk`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk0` FOREIGN KEY (`institutionFK`) REFERENCES `Institution`(`institutionPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk1` FOREIGN KEY (`subjectFK`) REFERENCES `Subject`(`subjectPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk2` FOREIGN KEY (`gradeFk`) REFERENCES `Grade`(`gradePK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk3` FOREIGN KEY (`subjectLevelFK`) REFERENCES `subjectLevel`(`subjectLevelPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk4` FOREIGN KEY (`ClassYearFK`) REFERENCES `Year`(`yearPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk5` FOREIGN KEY (`subjectAbbreviationFK`) REFERENCES `subjectAbbreviation`(`subjectAbbreviationPK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk0` FOREIGN KEY (`experienceYearFK`) REFERENCES `Year`(`yearPK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk4` FOREIGN KEY (`languageFourFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages`(`languagePK`);

