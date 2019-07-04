-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 04, 2019 at 06:24 AM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subjectAbbreviation`
--
ALTER TABLE `subjectAbbreviation`
  ADD PRIMARY KEY (`subjectAbbreviationPK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subjectAbbreviation`
--
ALTER TABLE `subjectAbbreviation`
  MODIFY `subjectAbbreviationPK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
