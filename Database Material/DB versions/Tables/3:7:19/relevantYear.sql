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
(0, 2015),
(1, 2016),
(2, 2017),
(3, 2018),
(4, 2019),
(5, 2020);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `relevantYear`
--
ALTER TABLE `relevantYear`
  ADD PRIMARY KEY (`relevantYearPK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `relevantYear`
--
ALTER TABLE `relevantYear`
  MODIFY `relevantYearPK` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
