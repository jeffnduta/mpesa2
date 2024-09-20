-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 20, 2024 at 01:09 PM
-- Server version: 10.6.17-MariaDB-cll-lve
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stretcha_kaz`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `name`, `phone`, `amount`, `transaction_id`, `status`, `created_at`) VALUES
(1, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 10:15:28'),
(2, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:02'),
(3, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:05'),
(4, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:10'),
(5, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:12'),
(6, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:15'),
(7, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:16:27'),
(8, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:20:53'),
(9, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:20:53'),
(10, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:21:00'),
(11, 'mpesa_callback.php', '254713446312', 45.00, NULL, NULL, '2024-09-18 11:23:20'),
(12, 'mpesa_callback.php', '254713446312', 20.00, NULL, NULL, '2024-09-18 11:40:29'),
(13, 'mpesa_callback.php', '254713446312', 20.00, NULL, NULL, '2024-09-18 11:43:51'),
(14, 'mpesa_callback.php', '254713446312', 20.00, NULL, NULL, '2024-09-18 11:51:44'),
(15, 'mpesa_callback.php', '254708374149', 20.00, NULL, NULL, '2024-09-18 12:02:12'),
(16, 'mpesa_callback.php', '254769622859', 20.00, NULL, NULL, '2024-09-18 12:04:03'),
(17, 'pius', '254739266718', 1.00, NULL, NULL, '2024-09-18 12:05:30'),
(18, 'pius', '254739266718', 1.00, NULL, NULL, '2024-09-18 12:26:36'),
(19, 'mpesa_callback.php', '254739266718', 45.00, NULL, NULL, '2024-09-18 12:29:27'),
(20, 'mpesa_callback.php', '254739266718', 45.00, NULL, NULL, '2024-09-18 12:33:06'),
(21, 'mpesa_callback.php', '254745340525', 45.00, NULL, NULL, '2024-09-18 12:41:31'),
(22, 'pius mwaniki', '254745340525', 1.00, NULL, NULL, '2024-09-18 12:44:21'),
(23, 'pius mwaniki', '254745340525', 1.00, NULL, NULL, '2024-09-18 12:47:20'),
(24, 'purity', '254745340525', 45.00, NULL, NULL, '2024-09-18 12:53:16'),
(25, 'pius', '254745340525', 1.00, NULL, NULL, '2024-09-18 13:07:24'),
(26, 'pius', '254745340525', 1.00, NULL, NULL, '2024-09-18 13:10:53'),
(27, 'jeff', '254704435545', 20.00, NULL, NULL, '2024-09-20 10:49:50'),
(28, 'jeff', '254704435545', 20.00, NULL, NULL, '2024-09-20 11:08:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
