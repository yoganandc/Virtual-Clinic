-- phpMyAdmin SQL Dump
-- version 4.4.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 09, 2015 at 03:05 PM
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
CREATE DATABASE IF NOT EXISTS `virtualclinic` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `virtualclinic`;

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
-- Table structure for table `vc_case`
--

CREATE TABLE IF NOT EXISTS `vc_case` (
  `case_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `altname` varchar(40) DEFAULT NULL,
  `chronic` bit(1) NOT NULL DEFAULT b'0',
  `patient_history` varchar(400) DEFAULT NULL,
  `past_history` varchar(400) DEFAULT NULL,
  `personal_history` varchar(400) DEFAULT NULL,
  `family_history` varchar(400) DEFAULT NULL,
  `examination` varchar(400) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edit_lock` bit(1) DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_case_file`
--

CREATE TABLE IF NOT EXISTS `vc_case_file` (
  `case_file_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `title` varchar(40) NOT NULL,
  `filename` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_complaint`
--

CREATE TABLE IF NOT EXISTS `vc_complaint` (
  `complaint_id` int(11) NOT NULL,
  `complaint` varchar(40) NOT NULL,
  `chronic_only` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_forward`
--

CREATE TABLE IF NOT EXISTS `vc_forward` (
  `forward_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `status` enum('0','1','2') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_messages`
--

CREATE TABLE IF NOT EXISTS `vc_messages` (
  `message_id` int(11) NOT NULL,
  `assigneduser_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(400) DEFAULT NULL
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
-- Table structure for table `vc_recents`
--

CREATE TABLE IF NOT EXISTS `vc_recents` (
  `recents_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_test`
--

CREATE TABLE IF NOT EXISTS `vc_test` (
  `test_id` int(11) NOT NULL,
  `test_name_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `altname` varchar(40) DEFAULT NULL,
  `result` varchar(40) NOT NULL,
  `filename` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_test_name`
--

CREATE TABLE IF NOT EXISTS `vc_test_name` (
  `test_name_id` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_treatment`
--

CREATE TABLE IF NOT EXISTS `vc_treatment` (
  `treatment_id` int(11) NOT NULL,
  `treatment_name_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `altname` varchar(40) DEFAULT NULL,
  `dosage` char(3) NOT NULL,
  `before_food` bit(1) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_treatment_name`
--

CREATE TABLE IF NOT EXISTS `vc_treatment_name` (
  `treatment_name_id` int(11) NOT NULL,
  `treatment_type_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `strength` decimal(6,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_treatment_type`
--

CREATE TABLE IF NOT EXISTS `vc_treatment_type` (
  `treatment_type_id` int(11) NOT NULL,
  `initial` char(1) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL
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
  `picture` varchar(20) NOT NULL DEFAULT 'default.png',
  `vc_recents_pointer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vc_user_status`
--

CREATE TABLE IF NOT EXISTS `vc_user_status` (
  `status_id` int(11) NOT NULL,
  `status` bit(1) NOT NULL DEFAULT b'0'
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
-- Indexes for table `vc_case`
--
ALTER TABLE `vc_case`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `vc_case_file`
--
ALTER TABLE `vc_case_file`
  ADD PRIMARY KEY (`case_file_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `vc_complaint`
--
ALTER TABLE `vc_complaint`
  ADD PRIMARY KEY (`complaint_id`);

--
-- Indexes for table `vc_forward`
--
ALTER TABLE `vc_forward`
  ADD PRIMARY KEY (`forward_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `case_id` (`case_id`);

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
-- Indexes for table `vc_recents`
--
ALTER TABLE `vc_recents`
  ADD PRIMARY KEY (`recents_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `vc_test`
--
ALTER TABLE `vc_test`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `test_name_id` (`test_name_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `vc_test_name`
--
ALTER TABLE `vc_test_name`
  ADD PRIMARY KEY (`test_name_id`);

--
-- Indexes for table `vc_treatment`
--
ALTER TABLE `vc_treatment`
  ADD PRIMARY KEY (`treatment_id`),
  ADD KEY `treatment_name_id` (`treatment_name_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `vc_treatment_name`
--
ALTER TABLE `vc_treatment_name`
  ADD PRIMARY KEY (`treatment_name_id`),
  ADD KEY `treatment_type_id` (`treatment_type_id`);

--
-- Indexes for table `vc_treatment_type`
--
ALTER TABLE `vc_treatment_type`
  ADD PRIMARY KEY (`treatment_type_id`);

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
-- AUTO_INCREMENT for table `vc_case`
--
ALTER TABLE `vc_case`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_case_file`
--
ALTER TABLE `vc_case_file`
  MODIFY `case_file_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_complaint`
--
ALTER TABLE `vc_complaint`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_forward`
--
ALTER TABLE `vc_forward`
  MODIFY `forward_id` int(11) NOT NULL AUTO_INCREMENT;
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
-- AUTO_INCREMENT for table `vc_recents`
--
ALTER TABLE `vc_recents`
  MODIFY `recents_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_test`
--
ALTER TABLE `vc_test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_test_name`
--
ALTER TABLE `vc_test_name`
  MODIFY `test_name_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_treatment`
--
ALTER TABLE `vc_treatment`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_treatment_name`
--
ALTER TABLE `vc_treatment_name`
  MODIFY `treatment_name_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vc_treatment_type`
--
ALTER TABLE `vc_treatment_type`
  MODIFY `treatment_type_id` int(11) NOT NULL AUTO_INCREMENT;
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
-- Constraints for table `vc_case`
--
ALTER TABLE `vc_case`
  ADD CONSTRAINT `vc_case_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `vc_complaint` (`complaint_id`),
  ADD CONSTRAINT `vc_case_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `vc_patient` (`patient_id`);

--
-- Constraints for table `vc_case_file`
--
ALTER TABLE `vc_case_file`
  ADD CONSTRAINT `vc_case_file_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `vc_case` (`case_id`);

--
-- Constraints for table `vc_forward`
--
ALTER TABLE `vc_forward`
  ADD CONSTRAINT `vc_forward_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `vc_user` (`user_id`),
  ADD CONSTRAINT `vc_forward_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `vc_case` (`case_id`);

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
-- Constraints for table `vc_recents`
--
ALTER TABLE `vc_recents`
  ADD CONSTRAINT `vc_recents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `vc_user` (`user_id`),
  ADD CONSTRAINT `vc_recents_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `vc_patient` (`patient_id`);

--
-- Constraints for table `vc_test`
--
ALTER TABLE `vc_test`
  ADD CONSTRAINT `vc_test_ibfk_1` FOREIGN KEY (`test_name_id`) REFERENCES `vc_test_name` (`test_name_id`),
  ADD CONSTRAINT `vc_test_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `vc_case` (`case_id`);

--
-- Constraints for table `vc_treatment`
--
ALTER TABLE `vc_treatment`
  ADD CONSTRAINT `vc_treatment_ibfk_1` FOREIGN KEY (`treatment_name_id`) REFERENCES `vc_treatment_name` (`treatment_name_id`),
  ADD CONSTRAINT `vc_treatment_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `vc_case` (`case_id`);

--
-- Constraints for table `vc_treatment_name`
--
ALTER TABLE `vc_treatment_name`
  ADD CONSTRAINT `vc_treatment_name_ibfk_1` FOREIGN KEY (`treatment_type_id`) REFERENCES `vc_treatment_type` (`treatment_type_id`);

--
-- Constraints for table `vc_user`
--
ALTER TABLE `vc_user`
  ADD CONSTRAINT `vc_user_ibfk_1` FOREIGN KEY (`assigneduser_id`) REFERENCES `vc_user` (`user_id`),
  ADD CONSTRAINT `vc_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `vc_user_status` (`status_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
