-- phpMyAdmin SQL Dump
-- version 4.4.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2015 at 06:18 AM
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

--
-- Dumping data for table `vc_address_state`
--

INSERT INTO `vc_address_state` (`state_id`, `code`, `name`) VALUES
(1, 'AP', 'Andhra Pradesh'),
(2, 'AR', 'Arunachal Pradesh'),
(3, 'AS', 'Assam'),
(4, 'BR', 'Bihar'),
(5, 'CG', 'Chhattisgarh'),
(6, 'GA', 'Goa'),
(7, 'GJ', 'Gujarat'),
(8, 'HR', 'Haryana'),
(9, 'HP', 'Himachal Pradesh'),
(10, 'JK', 'Jammu and Kashmir'),
(11, 'JH', 'Jharkhand'),
(12, 'KA', 'Karnataka'),
(13, 'KL', 'Kerala'),
(14, 'MP', 'Madhya Pradesh'),
(15, 'MH', 'Maharashtra'),
(16, 'MN', 'Manipur'),
(17, 'ML', 'Meghalaya'),
(18, 'MZ', 'Mizoram'),
(19, 'NL', 'Nagaland'),
(20, 'OD', 'Odisha'),
(21, 'PB', 'Punjab'),
(22, 'RJ', 'Rajasthan'),
(23, 'SK', 'Sikkim'),
(24, 'TN', 'Tamilnadu'),
(25, 'TS', 'Telangana'),
(26, 'TR', 'Tripura'),
(27, 'UK', 'Uttarakhand'),
(28, 'UP', 'Uttar Pradesh'),
(29, 'WB', 'West Bengal'),
(30, 'AN', 'Andaman and Nicobar Islands'),
(31, 'CH', 'Chandigarh'),
(32, 'DN', 'Dadra and Nagar Haveli'),
(33, 'DD', 'Daman and Diu'),
(34, 'DL', 'Delhi'),
(35, 'LD', 'Lakshadweep'),
(36, 'PY', 'Puducherry');

--
-- Dumping data for table `vc_admin`
--

INSERT INTO `vc_admin` (`password`) VALUES
('d033e22ae348aeb5660fc2140aec35850c4da997');

--
-- Dumping data for table `vc_test_name`
--

INSERT INTO `vc_test_name` (`test_name_id`, `name`) VALUES
(1, 'Blood Test'),
(2, 'Urine Test'),
(3, 'Ultrasound'),
(4, 'Not Listed');

--
-- Dumping data for table `vc_treatment_name`
--

INSERT INTO `vc_treatment_name` (`treatment_name_id`, `treatment_type_id`, `name`, `strength`) VALUES
(1, 1, 'Crocin', '500.000'),
(2, 2, 'Crocin DS', '240.000'),
(3, 1, 'Calpol', '500.000'),
(4, 2, 'Calpol', '120.000'),
(5, 1, 'Dolo', '500.000'),
(6, 2, 'Dolo', '125.000'),
(7, 1, 'Dolo 650', '650.000'),
(8, 1, 'Storvas', '5.000'),
(9, 1, 'Storvas', '10.000'),
(10, 1, 'Storvas', '20.000'),
(11, 1, 'Storvas', '40.000'),
(12, 1, 'Storvas', '80.000'),
(13, 1, 'Lipvas', '10.000'),
(14, 1, 'Lipvas', '20.000'),
(15, 1, 'Lipvas', '40.000'),
(16, 1, 'Lilo', '10.000'),
(17, 1, 'Acebitor', '5.000'),
(18, 1, 'Cipril', '5.000'),
(19, 1, 'Cipril', '12.500'),
(20, 1, 'Eltroxin', '0.100'),
(21, 1, 'Eltroxin', '0.050'),
(22, 1, 'Thyrowin', '0.025'),
(23, 1, 'Omez', '40.000'),
(24, 3, 'Omez', '40.000'),
(25, 1, 'Lomac', '10.000'),
(26, 1, 'Zithromax', '250.000'),
(27, 1, 'Zithromax', '500.000'),
(28, 1, 'Glycomet', '250.000'),
(29, 1, 'Glycomet', '500.000'),
(30, 1, 'Glycomet', '850.000'),
(31, 1, 'Tranax', '0.500'),
(32, 1, 'Tranax', '1.000'),
(33, 1, 'Tranax', '0.250'),
(34, 1, 'Stilnoct-CR', '12.500'),
(35, 1, 'Stilnoct-CR', '6.250'),
(36, 1, 'Sertil', '50.000'),
(37, 1, 'Sertil', '100.000'),
(38, 4, 'Not Listed', '0.000');

--
-- Dumping data for table `vc_treatment_type`
--

INSERT INTO `vc_treatment_type` (`treatment_type_id`, `initial`, `name`, `unit`) VALUES
(1, 'T', 'Tablet', 'mg'),
(2, 'S', 'Suspension', 'mg/5ml'),
(3, 'I', 'Injection', 'mg'),
(4, NULL, 'Not Listed', NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
