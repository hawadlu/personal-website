CREATE TABLE `Projects` (
	`uniqueKey` tinyint(2) NOT NULL UNIQUE,
	`name` varchar(30) NOT NULL,
	`projectYearFK` tinyint(2) NOT NULL,
	`link` varchar(100) NOT NULL,
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
	`credits` tinyint NOT NULL,
	`classYearFK` tinyint(2) NOT NULL,
	`subjectAbbreviationFK` tinyint(2) NOT NULL DEFAULT '2',
	`endorsementFK` tinyint(2) NOT NULL
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
	`subject` varchar(100) NOT NULL,
	PRIMARY KEY (`subjectPK`)
);

CREATE TABLE `Grade` (
	`gradePK` tinyint(2) NOT NULL,
	`grade` varchar(2) NOT NULL,
	PRIMARY KEY (`gradePK`)
);

CREATE TABLE `subjectLevel` (
	`subjectLevelPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`subjectLevel` varchar(20) NOT NULL,
	PRIMARY KEY (`subjectLevelPK`)
);

CREATE TABLE `subjectAbbreviation` (
	`subjectAbbreviationPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`subjectAbbreviation` varchar(10) NOT NULL,
	PRIMARY KEY (`subjectAbbreviationPK`)
);

CREATE TABLE `relevantYear` (
	`relevantYearPK` tinyint(2) NOT NULL AUTO_INCREMENT,
	`relevantYear` DATE NOT NULL,
	PRIMARY KEY (`relevantYearPK`)
);

CREATE TABLE `Endorsement` (
	`endorsementPK` tinyint(1) NOT NULL AUTO_INCREMENT,
	`endorsement` varchar(2) NOT NULL,
	PRIMARY KEY (`endorsementPK`)
);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk0` FOREIGN KEY (`projectYearFK`) REFERENCES `relevantYear`(`relevantYearPK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk4` FOREIGN KEY (`languageFourFk`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Projects` ADD CONSTRAINT `Projects_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk0` FOREIGN KEY (`institutionFK`) REFERENCES `Institution`(`institutionPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk1` FOREIGN KEY (`subjectFK`) REFERENCES `Subject`(`subjectPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk2` FOREIGN KEY (`gradeFk`) REFERENCES `Grade`(`gradePK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk3` FOREIGN KEY (`subjectLevelFK`) REFERENCES `subjectLevel`(`subjectLevelPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk4` FOREIGN KEY (`classYearFK`) REFERENCES `relevantYear`(`relevantYearPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk5` FOREIGN KEY (`subjectAbbreviationFK`) REFERENCES `subjectAbbreviation`(`subjectAbbreviationPK`);

ALTER TABLE `Education` ADD CONSTRAINT `Education_fk6` FOREIGN KEY (`endorsementFK`) REFERENCES `Endorsement`(`endorsementPK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk0` FOREIGN KEY (`experienceYearFK`) REFERENCES `relevantYear`(`relevantYearPK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk4` FOREIGN KEY (`languageFourFK`) REFERENCES `Languages`(`languagePK`);

ALTER TABLE `Experience` ADD CONSTRAINT `Experience_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages`(`languagePK`);

