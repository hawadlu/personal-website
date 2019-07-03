-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 03, 2019 at 11:33 PM
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
  `key` tinyint(4) NOT NULL,
  `institutionFK` tinyint(2) NOT NULL,
  `subjectFK` tinyint(2) NOT NULL,
  `gradeFk` tinyint(2) NOT NULL,
  `Credits` tinyint(4) NOT NULL,
  `Year` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Experience`
--

CREATE TABLE `Experience` (
  `key` tinyint(2) NOT NULL,
  `name` varchar(30) NOT NULL,
  `experienceYear` date NOT NULL,
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
  `Grade` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Institution`
--

CREATE TABLE `Institution` (
  `institutionPK` tinyint(2) NOT NULL,
  `institution` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE `Languages` (
  `languagePK` tinyint(2) NOT NULL,
  `language` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `name` varchar(30) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `projectYear` year(4) NOT NULL,
  `languageFK` tinyint(2) NOT NULL,
  `Link` varchar(100) NOT NULL,
  `projectDescription` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Subject`
--

CREATE TABLE `Subject` (
  `subjectPK` tinyint(2) NOT NULL,
  `Subject` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Education`
--
ALTER TABLE `Education`
  ADD UNIQUE KEY `key` (`key`),
  ADD UNIQUE KEY `institutionFK` (`institutionFK`),
  ADD UNIQUE KEY `subjectFK` (`subjectFK`),
  ADD UNIQUE KEY `gradeFk` (`gradeFk`);

--
-- Indexes for table `Experience`
--
ALTER TABLE `Experience`
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `Experience_fk0` (`languageOneFK`),
  ADD KEY `Experience_fk1` (`languageTwoFK`),
  ADD KEY `Experience_fk2` (`languageThreeFK`),
  ADD KEY `Experience_fk3` (`languageFourFK`),
  ADD KEY `Experience_fk4` (`languageFiveFK`);

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
  ADD KEY `Projects_fk0` (`languageFK`);

--
-- Indexes for table `Subject`
--
ALTER TABLE `Subject`
  ADD PRIMARY KEY (`subjectPK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Languages`
--
ALTER TABLE `Languages`
  MODIFY `languagePK` tinyint(2) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Education`
--
ALTER TABLE `Education`
  ADD CONSTRAINT `Education_fk0` FOREIGN KEY (`institutionFK`) REFERENCES `Institution` (`institutionPK`),
  ADD CONSTRAINT `Education_fk1` FOREIGN KEY (`subjectFK`) REFERENCES `Subject` (`subjectPK`),
  ADD CONSTRAINT `Education_fk2` FOREIGN KEY (`gradeFk`) REFERENCES `Grade` (`gradePK`);

--
-- Constraints for table `Experience`
--
ALTER TABLE `Experience`
  ADD CONSTRAINT `Experience_fk0` FOREIGN KEY (`languageOneFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk1` FOREIGN KEY (`languageTwoFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk2` FOREIGN KEY (`languageThreeFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk3` FOREIGN KEY (`languageFourFK`) REFERENCES `Languages` (`languagePK`),
  ADD CONSTRAINT `Experience_fk4` FOREIGN KEY (`languageFiveFK`) REFERENCES `Languages` (`languagePK`);

--
-- Constraints for table `Projects`
--
ALTER TABLE `Projects`
  ADD CONSTRAINT `Projects_fk0` FOREIGN KEY (`languageFK`) REFERENCES `Languages` (`languagePK`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
