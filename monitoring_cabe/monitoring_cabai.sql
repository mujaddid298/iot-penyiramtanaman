-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 06:42 PM
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
-- Database: `monitoring_cabai`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_monitoring`
--

CREATE TABLE `data_monitoring` (
  `id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `suhu` decimal(5,2) NOT NULL,
  `kelembapan` decimal(5,2) NOT NULL,
  `status_mesin` enum('Aktif','Tidak Aktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_monitoring`
--

INSERT INTO `data_monitoring` (`id`, `tanggal`, `suhu`, `kelembapan`, `status_mesin`) VALUES
(1, '2025-04-27 08:00:00', 28.50, 65.20, 'Tidak Aktif'),
(2, '2025-04-27 10:30:00', 30.20, 60.50, 'Aktif'),
(3, '2025-04-27 13:00:00', 32.70, 55.30, 'Aktif'),
(4, '2025-04-27 15:30:00', 31.40, 58.70, 'Tidak Aktif'),
(5, '2025-04-27 18:00:00', 29.10, 62.40, 'Tidak Aktif'),
(6, '2025-04-27 08:00:00', 28.50, 65.20, 'Tidak Aktif'),
(7, '2025-04-27 10:30:00', 30.20, 60.50, 'Aktif'),
(8, '2025-04-27 13:00:00', 32.70, 55.30, 'Aktif'),
(9, '2025-04-27 15:30:00', 31.40, 58.70, 'Tidak Aktif'),
(10, '2025-04-27 18:00:00', 29.10, 62.40, 'Tidak Aktif'),
(11, '2025-04-28 07:30:00', 27.80, 67.50, 'Tidak Aktif'),
(12, '2025-04-28 09:45:00', 29.50, 63.80, 'Aktif'),
(13, '2025-04-28 12:15:00', 31.90, 57.20, 'Aktif'),
(14, '2025-04-28 14:45:00', 32.30, 54.60, 'Aktif'),
(15, '2025-04-28 17:30:00', 30.10, 59.80, 'Tidak Aktif'),
(16, '2025-04-29 08:15:00', 28.20, 64.90, 'Tidak Aktif'),
(17, '2025-04-29 10:45:00', 30.60, 61.30, 'Aktif'),
(18, '2025-04-29 13:30:00', 33.10, 53.80, 'Aktif'),
(19, '2025-04-29 16:00:00', 31.70, 56.40, 'Aktif'),
(20, '2025-04-29 18:30:00', 29.40, 61.70, 'Tidak Aktif'),
(21, '2025-04-30 07:45:00', 27.50, 68.20, 'Tidak Aktif'),
(22, '2025-04-30 10:00:00', 29.80, 62.50, 'Aktif'),
(23, '2025-04-30 12:45:00', 32.40, 55.70, 'Aktif'),
(24, '2025-04-30 15:15:00', 31.20, 58.30, 'Aktif'),
(25, '2025-04-30 17:45:00', 28.90, 63.10, 'Tidak Aktif'),
(26, '2025-05-01 08:30:00', 28.30, 66.40, 'Tidak Aktif'),
(27, '2025-05-01 11:00:00', 30.70, 60.80, 'Aktif'),
(28, '2025-05-01 13:45:00', 33.50, 52.90, 'Aktif'),
(29, '2025-05-01 16:15:00', 32.10, 57.50, 'Aktif'),
(30, '2025-05-01 18:45:00', 29.70, 62.80, 'Tidak Aktif'),
(31, '2025-05-02 07:15:00', 27.90, 67.80, 'Tidak Aktif'),
(32, '2025-05-02 09:30:00', 29.30, 64.20, 'Aktif'),
(33, '2025-05-02 12:00:00', 31.80, 58.10, 'Aktif'),
(34, '2025-05-02 14:30:00', 32.60, 54.20, 'Aktif'),
(35, '2025-05-02 17:00:00', 30.30, 60.10, 'Tidak Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(4, 'zahra', '$2y$10$54kqJCxtOmjp5dD7rwe2pOq4DHekvMnyS6QqJPR01ef4x/T0c9qGS', 'zahra@gmail.com', '2025-04-27 22:04:58'),
(5, 'rara', 'rara0909', 'rara@gmail.com', '2025-04-27 23:13:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_monitoring`
--
ALTER TABLE `data_monitoring`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_monitoring`
--
ALTER TABLE `data_monitoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
