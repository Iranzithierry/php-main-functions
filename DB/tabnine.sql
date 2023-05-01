-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2023 at 12:17 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tabnine`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(106, 'true', '$2y$10$1m/4ybclHwfFHYicAuQ9T.3Cx0ootqkXyHu24nFAACPBbOvsiWDHy');

-- --------------------------------------------------------

--
-- Table structure for table `recycle_bin`
--

CREATE TABLE `recycle_bin` (
  `id` int(11) NOT NULL,
  `fname` text NOT NULL,
  `sname` text NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `username` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recycle_bin`
--

INSERT INTO `recycle_bin` (`id`, `fname`, `sname`, `mobile_number`, `email`, `username`, `password`, `deleted_at`, `code`) VALUES
(3, 'Iranzi', 'Thierry', '0788451370', 'vpnzo7@gmail.com', 'true', '$2y$10$SSx05ZThxC2uj3zIkcLB7eY0CvpRXoHo52420k/OnpQu0JCDAwFxK', '2023-04-29 09:55:32', '6018'),
(5, 'test', 'test', '078888888', 'jk@gmail.com', 'test', '$2y$10$6U2fGBNfj1kTqApKu6C5Su5l7H2oKPBa.VXJE7KnkED7qr0kaf1aG', '2023-04-29 09:56:03', '9470');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` text NOT NULL,
  `sname` text NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `username` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` text DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `banned_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `sname`, `mobile_number`, `email`, `username`, `password`, `code`, `joined_at`, `banned_until`) VALUES
(9, 'ski', 'ska', '0788541307', 'jk@gmail.com', 'yolla', '$2y$10$PMZ7p.3DqBCKPaYitpdGRe9FPvXwqGx2vm5gcVLunOtbJXjBFmpOm', '4751', '2023-04-29 09:56:35', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `id_3` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indexes for table `recycle_bin`
--
ALTER TABLE `recycle_bin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `recycle_bin`
--
ALTER TABLE `recycle_bin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
