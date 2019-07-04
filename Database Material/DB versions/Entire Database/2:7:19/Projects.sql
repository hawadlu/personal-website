-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 02, 2019 at 01:33 AM
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
-- Database: `Projects`
--

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
(1, 'Web');

-- --------------------------------------------------------

--
-- Table structure for table `Primary`
--

CREATE TABLE `Primary` (
  `name` varchar(30) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `projectYear` year(4) NOT NULL,
  `languageFK` tinyint(2) NOT NULL,
  `link` varchar(100) NOT NULL,
  `uniqueKey` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Primary`
--

INSERT INTO `Primary` (`name`, `description`, `projectYear`, `languageFK`, `link`, `uniqueKey`) VALUES
('myum House', 'Testing', 2018, 1, 'yumyumhouselimited.co.nz', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Languages`
--
ALTER TABLE `Languages`
  ADD PRIMARY KEY (`languagePK`);

--
-- Indexes for table `Primary`
--
ALTER TABLE `Primary`
  ADD UNIQUE KEY `uniqueKey` (`uniqueKey`),
  ADD KEY `Primary_fk0` (`languageFK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Languages`
--
ALTER TABLE `Languages`
  MODIFY `languagePK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Primary`
--
ALTER TABLE `Primary`
  ADD CONSTRAINT `Primary_fk0` FOREIGN KEY (`languageFK`) REFERENCES `Languages` (`languagePK`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
