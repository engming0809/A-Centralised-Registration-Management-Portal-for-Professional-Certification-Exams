-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2024 at 04:02 PM
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
-- Database: `cert_reg_management_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificationregistrations`
--

CREATE TABLE `certificationregistrations` (
  `registration_id` int(11) NOT NULL,
  `registration_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `student_id` int(11) DEFAULT NULL,
  `certification_id` int(11) DEFAULT NULL,
  `notification` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificationregistrations`
--

INSERT INTO `certificationregistrations` (`registration_id`, `registration_status`, `created_at`, `updated_at`, `student_id`, `certification_id`, `notification`) VALUES
(1, 'examletter_submitted', '2024-11-02 08:45:00', '2024-11-03 14:50:34', 2, 1, 1),
(3, 'transaction_submitted', '2024-11-02 19:26:18', '2024-11-03 14:54:17', 3, 2, 0),
(4, 'transaction_submitted', '2024-11-03 14:38:57', '2024-11-03 14:53:46', 2, 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificationregistrations`
--
ALTER TABLE `certificationregistrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `certification_id` (`certification_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificationregistrations`
--
ALTER TABLE `certificationregistrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificationregistrations`
--
ALTER TABLE `certificationregistrations`
  ADD CONSTRAINT `certificationregistrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `certificationregistrations_ibfk_2` FOREIGN KEY (`certification_id`) REFERENCES `certifications` (`certification_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
