-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2026 at 09:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ewu_lost_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(4, 'Accessories'),
(3, 'Bags'),
(2, 'Documents'),
(1, 'Electronics'),
(5, 'Keys'),
(6, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `claim`
--

CREATE TABLE `claim` (
  `claim_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `claimant_id` int(11) NOT NULL,
  `claim_reason` text NOT NULL,
  `claim_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `verified_by` int(11) DEFAULT NULL,
  `verification_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claim`
--

INSERT INTO `claim` (`claim_id`, `item_id`, `claimant_id`, `claim_reason`, `claim_date`, `status`, `verified_by`, `verification_notes`) VALUES
(1, 4, 8, '', '2026-08-15', 'Rejected', 2, 'I can identify my calculator by the sticker on the back.');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `report_type` enum('Lost','Found') NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `location` varchar(200) NOT NULL,
  `date_occurred` date NOT NULL,
  `date_reported` date NOT NULL,
  `status` enum('Pending','Approved','Rejected','Matched','Claimed','Closed') NOT NULL DEFAULT 'Pending',
  `reported_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `matched_item_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `report_type`, `title`, `description`, `category_id`, `location`, `date_occurred`, `date_reported`, `status`, `reported_by`, `approved_by`, `matched_item_id`, `image_path`) VALUES
(1, 'Lost', 'Black Wallet', 'Lost my black leather wallet near the library entrance. Contains student ID and some cash.', 4, 'Library Entrance', '2026-08-10', '2026-08-11', 'Pending', 6, NULL, NULL, NULL),
(2, 'Found', 'iPhone 13 - Blue', 'Found a blue iPhone 13 on a bench outside the cafeteria. Screen has a small crack.', 1, 'Cafeteria', '2026-08-12', '2026-08-12', 'Pending', 7, NULL, NULL, NULL),
(3, 'Lost', 'Student ID Card', 'Lost my EWU student ID card somewhere between the CSE building and parking lot.', 2, 'CSE Building', '2026-08-13', '2026-08-13', 'Approved', 8, NULL, NULL, NULL),
(4, 'Found', 'Grey Backpack', 'Found a grey backpack left in Room 402. Contains some notebooks and a calculator.', 3, 'Room 402', '2026-08-14', '2026-08-14', 'Approved', 9, NULL, NULL, NULL),
(5, 'Lost', 'House Keys with Keychain', 'Lost my house keys with a red keychain near the main gate.', 5, 'Main Gate', '2026-08-09', '2026-08-10', 'Closed', 6, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('Admin','Student') NOT NULL DEFAULT 'Student',
  `department` varchar(100) DEFAULT NULL,
  `registration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone`, `role`, `department`, `registration_date`) VALUES
(2, 'Admin User', '2024-3-60-066@std.ewubd.edu', '$2y$10$j9IMcUxQRE8SXU2P/QgTHeJkciTILFjqwQ2ftocxCvhz3aTJbfQPW', '01612052867', 'Admin', 'CSE', '2026-08-15'),
(6, 'Rifat Hasan', '2020-1-60-045@std.ewubd.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01711223344', 'Student', 'CSE', '2026-01-15'),
(7, 'Sadia Islam', '2020-2-60-102@std.ewubd.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01822334455', 'Student', 'BBA', '2026-01-20'),
(8, 'Tanvir Ahmed', '2019-1-60-089@std.ewubd.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01933445566', 'Student', 'EEE', '2026-02-01'),
(9, 'Mim Akter', '2021-3-60-015@std.ewubd.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01644556677', 'Student', 'CSE', '2026-02-10'),
(11, 'Mst.Jannatul haque', '2023-3-60-066@std.ewubd.edu', '$2y$10$txITrDSJ.gWJiE4iGVOAdOxjQfXbOGvaUnr4oEH2sfjriPSxQqUC6', '', 'Student', 'CSE', '2026-08-15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `claim`
--
ALTER TABLE `claim`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `claimant_id` (`claimant_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `matched_item_id` (`matched_item_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `claim`
--
ALTER TABLE `claim`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claim`
--
ALTER TABLE `claim`
  ADD CONSTRAINT `claim_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `claim_ibfk_2` FOREIGN KEY (`claimant_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `claim_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `item_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `item_ibfk_4` FOREIGN KEY (`matched_item_id`) REFERENCES `item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
