-- phpMyAdmin SQL Dump
-- version 4.4.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 04, 2015 at 04:42 AM
-- Server version: 5.6.22-log
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `virtualclinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `vc_address`
--

CREATE TABLE IF NOT EXISTS `vc_address` (
  `address_id` int(11) NOT NULL,
  `line1` varchar(80) DEFAULT NULL,
  `line2` varchar(80) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `district` varchar(40) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `pincode` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_address_state`
--

CREATE TABLE IF NOT EXISTS `vc_address_state` (
  `state_id` int(11) NOT NULL,
  `code` char(2) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_admin`
--

CREATE TABLE IF NOT EXISTS `vc_admin` (
  `password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_messages`
--

CREATE TABLE IF NOT EXISTS `vc_messages` (
  `message_id` int(11) NOT NULL,
  `assigneduser_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(400) DEFAULT NULL,
  `received` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_patient`
--

CREATE TABLE IF NOT EXISTS `vc_patient` (
  `patient_id` int(11) NOT NULL,
  `fname` varchar(40) NOT NULL DEFAULT 'VC',
  `lname` varchar(40) NOT NULL DEFAULT 'Patient',
  `gender` enum('m','f') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `occupation` varchar(40) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `picture` varchar(20) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_user`
--

CREATE TABLE IF NOT EXISTS `vc_user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `type` enum('t','d') NOT NULL,
  `assigneduser_id` int(11) DEFAULT NULL,
  `fname` varchar(40) NOT NULL DEFAULT 'VC',
  `lname` varchar(40) NOT NULL DEFAULT 'User',
  `gender` enum('m','f') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `picture` varchar(20) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_user_status`
--

CREATE TABLE IF NOT EXISTS `vc_user_status` (
  `status_id` int(11) NOT NULL,
  `status` bit(1) NOT NULL DEFAULT b'0',
  `room` char(40) DEFAULT NULL,
  `lastseen` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vc_address`
--
ALTER TABLE `vc_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `vc_address_state`
--
ALTER TABLE `vc_address_state`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `vc_messages`
--
ALTER TABLE `vc_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `assigneduser_id` (`assigneduser_id`);

--
-- Indexes for table `vc_patient`
--
ALTER TABLE `vc_patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `vc_user`
--
ALTER TABLE `vc_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `assigneduser_id` (`assigneduser_id`);

--
-- Indexes for table `vc_user_status`
--
ALTER TABLE `vc_user_status`
  ADD PRIMARY KEY (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vc_address`
--
ALTER TABLE `vc_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_address_state`
--
ALTER TABLE `vc_address_state`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_messages`
--
ALTER TABLE `vc_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_patient`
--
ALTER TABLE `vc_patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_user`
--
ALTER TABLE `vc_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_user_status`
--
ALTER TABLE `vc_user_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `vc_address`
--
ALTER TABLE `vc_address`
  ADD CONSTRAINT `vc_address_ibfk_1` FOREIGN KEY (`state_id`) REFERENCES `vc_address_state` (`state_id`);

--
-- Constraints for table `vc_messages`
--
ALTER TABLE `vc_messages`
  ADD CONSTRAINT `vc_messages_ibfk_1` FOREIGN KEY (`assigneduser_id`) REFERENCES `vc_user` (`user_id`);

--
-- Constraints for table `vc_patient`
--
ALTER TABLE `vc_patient`
  ADD CONSTRAINT `vc_patient_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `vc_address` (`address_id`);

--
-- Constraints for table `vc_user`
--
ALTER TABLE `vc_user`
  ADD CONSTRAINT `vc_user_ibfk_1` FOREIGN KEY (`assigneduser_id`) REFERENCES `vc_user` (`user_id`),
  ADD CONSTRAINT `vc_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `vc_user_status` (`status_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
