-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 04, 2019 at 06:47 AM
-- Server version: 10.3.15-MariaDB
-- PHP Version: 7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `personalWebsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `Education`
--

CREATE TABLE `Education` (
  `uniqueKey` tinyint(4) NOT NULL,
  `institutionFK` tinyint(2) NOT NULL,
  `subjectFK` tinyint(2) NOT NULL,
  `gradeFk` tinyint(2) NOT NULL,
  `subjectLevelFK` tinyint(2) NOT NULL,
  `credits` tinyint(4) DEFAULT NULL,
  `classYearFK` tinyint(2) NOT NULL,
  `subjectAbbreviationFK` tinyint(2) NOT NULL DEFAULT 2,
  `endorsementFK` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Education`
--

INSERT INTO `Education` (`uniqueKey`, `institutionFK`, `subjectFK`, `gradeFk`, `subjectLevelFK`, `credits`, `classYearFK`, `subjectAbbreviationFK`, `endorsementFK`) VALUES
(0, 1, 0, 11, 1, 20, 3, 0, 3),
(1, 1, 1, 11, 1, 20, 3, 1, 3),
(2, 1, 2, 11, 1, 14, 3, 2, 2),
(3, 1, 3, 11, 1, 17, 3, 3, 3),
(4, 1, 4, 11, 1, 17, 3, 4, 3),
(5, 1, 5, 11, 0, 28, 2, 5, 3),
(6, 1, 0, 11, 0, 18, 2, 0, 1),
(7, 1, 6, 11, 0, 20, 2, 6, 3),
(8, 1, 3, 11, 0, 19, 2, 3, 3),
(9, 1, 4, 11, 0, 26, 2, 4, 3),
(10, 0, 8, 1, 2, NULL, 4, 9, 3),
(11, 0, 9, 1, 2, NULL, 4, 10, 3),
(12, 0, 10, 2, 2, NULL, 4, 8, 3),
(13, 0, 11, 5, 2, NULL, 4, 11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `Endorsement`
--

CREATE TABLE `Endorsement` (
  `endorsementPK` tinyint(1) NOT NULL,
  `endorsement` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Endorsement`
--

INSERT INTO `Endorsement` (`endorsementPK`, `endorsement`) VALUES
(1, 'M'),
(2, 'E'),
(3, 'NA'),
(7, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `Experience`
--

CREATE TABLE `Experience` (
  `uniqueKey` tinyint(2) NOT NULL,
  `name` varchar(30) NOT NULL,
  `experienceYearFK` tinyint(2) NOT NULL,
  `experienceDescription` varchar(255) NOT NULL,
  `languageOneFK` tinyint(2) NOT NULL,
  `languageTwoFK` tinyint(2) NOT NULL,
  `languageThreeFK` tinyint(2) NOT NULL,
  `languageFourFK` tinyint(2) NOT NULL,
  `languageFiveFK` tinyint(2) NOT NULL,
  `Link` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Grade`
--

CREATE TABLE `Grade` (
  `gradePK` tinyint(2) NOT NULL,
  `grade` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Grade`
--

INSERT INTO `Grade` (`gradePK`, `grade`) VALUES
(0, 'A+'),
(1, 'A'),
(2, 'A-'),
(3, 'B+'),
(4, 'B'),
(5, 'B-'),
(6, 'C+'),
(7, 'C'),
(8, 'C-'),
(9, 'D'),
(10, 'E'),
(11, 'NA');

-- --------------------------------------------------------

--
-- Table structure for table `Institution`
--

CREATE TABLE `Institution` (
  `institutionPK` tinyint(2) NOT NULL,
  `institution` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE `Languages` (
  `languagePK` tinyint(2) NOT NULL,
  `language` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Languages`
--

INSERT INTO `Languages` (`languagePK`, `language`) VALUES
(0, 'CSS'),
(1, 'HTML'),
(2, 'JavaScript'),
(3, 'Java'),
(4, 'Kotlin'),
(5, 'PHP'),
(6, 'SQL'),
(7, 'WordPress'),
(8, 'Python'),
(9, 'C++'),
(10, 'NA');

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `uniqueKey` tinyint(2) NOT NULL,
  `name` varchar(30) NOT NULL,
  `projectYearFK` tinyint(2) NOT NULL,
  `link` varchar(100) NOT NULL,
  `projectDescription` varchar(255) NOT NULL,
  `languageOneFK` tinyint(2) NOT NULL,
  `languageTwoFK` tinyint(2) NOT NULL,
  `languageThreeFK` tinyint(2) NOT NULL,
  `languageFourFk` tinyint(2) NOT NULL,
  `languageFiveFK` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relevantYear`
--

CREATE TABLE `relevantYear` (
  `relevantYearPK` tinyint(2) NOT NULL,
  `relevantYear` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `relevantYear`
--

INSERT INTO `relevantYear` (`relevantYearPK`, `relevantYear`) VALUES
(1, 2016),
(2, 2017),
(3, 2018),
(4, 2019),
(5, 2020),
(7, 2015);

-- --------------------------------------------------------

--
-- Table structure for table `Subject`
--

CREATE TABLE `Subject` (
  `subjectPK` tinyint(2) NOT NULL,
  `subject` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Subject`
--

INSERT INTO `Subject` (`subjectPK`, `subject`) VALUES
(0, 'Classical Studies'),
(1, 'Computer Science and Information'),
(2, 'Design Information Technology'),
(3, 'Mathematics'),
(4, 'Physics'),
(5, 'Automotive Studies'),
(6, 'Digital Information Technology and Computer Science'),
(7, 'English'),
(8, 'Introduction to Computer Science'),
(9, 'Cybersecurity Fundamentals'),
(10, 'Engineering Technology'),
(11, 'Engineering Math Foundations');

-- --------------------------------------------------------

--
-- Table structure for table `subjectAbbreviation`
--

CREATE TABLE `subjectAbbreviation` (
  `subjectAbbreviationPK` tinyint(2) NOT NULL,
  `subjectAbbreviation` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subjectAbbreviation`
--

INSERT INTO `subjectAbbreviation` (`subjectAbbreviationPK`, `subjectAbbreviation`) VALUES
(0, 'CLS'),
(1, 'CSI'),
(2, 'DIT'),
(3, 'MAT'),
(4, 'PHY'),
(5, 'AUT'),
(6, 'DDIT'),
(7, 'ENG'),
(8, 'ENGR101'),
(9, 'COMP112'),
(10, 'CYBR171'),
(11, 'ENGR121');

-- --------------------------------------------------------

--
-- Table structure for table `subjectLevel`
--

CREATE TABLE `subjectLevel` (
  `subjectLevelPK` tinyint(2) NOT NULL,
  `subjectLevel` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subjectLevel`
--

INSERT INTO `subjectLevel` (`subjectLevelPK`, `subjectLevel`) VALUES
(0, 'NCEA Level Two'),
(1, 'NCEA Level Three'),
(2, 'Year One University');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Education`
--
ALTER TABLE `Education`
  ADD UNIQUE KEY `uniqueKey` (`uniqueKey`),
  ADD KEY `Education_fk0` (`institutionFK`),
  ADD KEY `Education_fk1` (`subjectFK`),
  ADD KEY `Education_fk2` (`gradeFk`),
  ADD KEY `Education_fk3` (`subjectLevelFK`),
  ADD KEY `Education_fk4` (`classYearFK`),
  ADD KEY `Education_fk5` (`subjectAbbreviationFK`),
  ADD KEY `Education_fk6` (`endorsementFK`);

--
-- Indexes for table `Endorsement`
--
ALTER TABLE `Endorsement`
  ADD PRIMARY KEY (`endorsementPK`);

--
-- Indexes for table `Experience`
--
ALTER TABLE `Experience`
  ADD UNIQUE KEY `uniqueKey` (`uniqueKey`),
  ADD KEY `Experience_fk0` (`experienceYearFK`),
  ADD KEY `Experience_fk1` (`languageOneFK`),
  ADD KEY `Experience_fk2` (`languageTwoFK`),
  ADD KEY `Experience_fk3` (`languageThreeFK`),
  ADD KEY `Experience_fk4` (`languageFourFK`),
  ADD KEY `Experience_fk5` (`languageFiveFK`);

--
-- Indexes for table `Grade`
--
ALTER TABLE `Grade`
  ADD PRIMARY KEY (`gradePK`);

--
-- Indexes for table `Institution`
--
ALTER TABLE `Institution`
  ADD PRIMARY KEY (`institutionPK`);

--
-- Indexes for table `Languages`
--
ALTER TABLE `Languages`
  ADD PRIMARY KEY (`languagePK`);

--
-- Indexes for table `Projects`
--
ALTER TABLE `Projects`
  ADD UNIQUE KEY `uniqueKey` (`uniqueKey`),
  ADD KEY `Projects_fk0` (`projectYearFK`),
  ADD KEY `Projects_fk1` (`languageOneFK`),
  ADD KEY `Projects_fk2` (`languageTwoFK`),
  ADD KEY `Projects_fk3` (`languageThreeFK`),
  ADD KEY `Projects_fk4` (`languageFourFk`),
  ADD KEY `Projects_fk5` (`languageFiveFK`);

--
-- Indexes for table `relevantYear`
--
ALTER TABLE `relevantYear`
  ADD PRIMARY KEY (`relevantYearPK`);

--
-- Indexes for table `Subject`
--
ALTER TABLE `Subject`
  ADD PRIMARY KEY (`subjectPK`);

--
-- Indexes for table `subjectAbbreviation`
--
ALTER TABLE `subjectAbbreviation`
  ADD PRIMARY KEY (`subjectAbbreviationPK`);

--
-- Indexes for table `subjectLevel`
--
ALTER TABLE `subjectLevel`
  ADD PRIMARY KEY (`subjectLevelPK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Endorsement`
--
ALTER TABLE `Endorsement`
  MODIFY `endorsementPK` tinyint(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Languages`
--
ALTER TABLE `Languages`
  MODIFY `languagePK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `relevantYear`
--
ALTER TABLE `relevantYear`
  MODIFY `relevantYearPK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subjectAbbreviation`
--
ALTER TABLE `subjectAbbreviation`
  MODIFY `subjectAbbreviationPK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `subjectLevel`
--
ALTER TABLE `subjectLevel`
  MODIFY `subjectLevelPK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Education`
--
ALTER TABLE `Education`
  ADD CONSTRAINT `Education_fk0` FOREIGN KEY (`institutionFK`) REFERENCES `Institution` (`institutionPK`),
  ADD CONSTRAINT `Education_fk1` FOREIGN KEY (`subjectFK`) REFERENCES `Subject` (`subjectPK`),
  ADD CONSTRAINT `Education_fk2` FOREIGN KEY (`gradeFk`) REFERENCES `Grade` (`gradePK`),
  ADD CONSTRAINT `Education_fk3` FOREIGN KEY (`subjectLevelFK`) REFERENCES `subjectLevel` (`subjectLevelPK`),
  ADD CONSTRAINT `Education_fk4` FOREIGN KEY (`classYearFK`) REFERENCES `relevantYear` (`relevantYearPK`),
  ADD CONSTRAINT `Education_fk5` FOREIGN KEY (`subjectAbbreviationFK`) REFERENCES `subjectAbbreviation` (`subjectAbbreviationPK`),
  ADD CONSTRAINT `Education_fk6` FOREIGN KEY (`endorsementFK`) REFERENCES `Endorsement` (`endorsementPK`);

--
-- Constraints for table `Experience`
--
ALTER TABLE `Experience`
  ADD CONSTRAINT `Experience_fk0` FOREIGN KEY (`experienceYearFK`) REFERENCES `relevantYear` (`relevantYearPK`),
  ADD CONSTRAINT `Experience_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk4` FOREIGN KEY (`languageFourFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages` (`languagePK`);

--
-- Constraints for table `Projects`
--
ALTER TABLE `Projects`
  ADD CONSTRAINT `Projects_fk0` FOREIGN KEY (`projectYearFK`) REFERENCES `relevantYear` (`relevantYearPK`),
  ADD CONSTRAINT `Projects_fk1` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Projects_fk2` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Projects_fk3` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Projects_fk4` FOREIGN KEY (`languageFourFk`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Projects_fk5` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages` (`languagePK`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
