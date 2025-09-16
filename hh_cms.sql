-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 01:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hh_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminusers`
--

CREATE TABLE `adminusers` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_hash` varchar(255) DEFAULT NULL,
  `verification_hash` varchar(255) DEFAULT NULL,
  `authorization_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminusers`
--

INSERT INTO `adminusers` (`id`, `name`, `email`, `password`, `profile_hash`, `verification_hash`, `authorization_hash`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, '2025-09-15 11:22:59'),
(3, 'Shoaib Akhtar', 'shoaib@reliantsurveyors.com', '$2y$10$eBQK2Xqx2mqwTB42sCg90eqQaWWpqQhycUedxTcJhFcKXO/HfClno', NULL, NULL, NULL, '2025-09-15 12:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `area_details`
--

CREATE TABLE `area_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` varchar(255) NOT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `property_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `developer_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `about_details` longtext DEFAULT NULL,
  `about_developer` longtext DEFAULT NULL,
  `starting_price` varchar(255) DEFAULT NULL,
  `payment_plan` varchar(255) DEFAULT NULL,
  `handover_date` date DEFAULT NULL,
  `area_title` varchar(255) DEFAULT NULL,
  `area_heading` varchar(255) DEFAULT NULL,
  `area_description` longtext DEFAULT NULL,
  `amenities` longtext DEFAULT NULL,
  `project_title_2` varchar(255) DEFAULT NULL,
  `project_title_3` varchar(255) DEFAULT NULL,
  `price_from` varchar(255) DEFAULT NULL,
  `handover_date_3` date DEFAULT NULL,
  `location_3` varchar(255) DEFAULT NULL,
  `development_time` varchar(255) DEFAULT NULL,
  `project_description_2` longtext DEFAULT NULL,
  `down_payment` varchar(255) DEFAULT NULL,
  `pre_handover` varchar(255) DEFAULT NULL,
  `handover` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `area_detail_files`
--

CREATE TABLE `area_detail_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_detail_id` int(11) NOT NULL,
  `file_key` varchar(100) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(150) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_data` longblob NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_area_detail` (`area_detail_id`),
  CONSTRAINT `fk_area_detail` FOREIGN KEY (`area_detail_id`) REFERENCES `area_details` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminusers`
--
ALTER TABLE `adminusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminusers`
--
ALTER TABLE `adminusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
