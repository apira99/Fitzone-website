-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2025 at 05:56 AM
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
-- Database: `fitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `client_email` varchar(255) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `class` varchar(100) NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled','completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `client_email`, `appointment_date`, `class`, `status`) VALUES
(1, 'heira@gmail.com', '2025-01-04 00:17:00', 'Pilates', 'Cancelled'),
(2, 'heira@gmail.com', '2025-01-19 00:24:00', 'Cardio Blast', 'Confirmed'),
(3, 'simbaa@gmail.com', '2025-01-09 09:00:00', 'cardio exercise', 'completed'),
(4, 'simbaa@gmail.com', '2025-01-12 10:15:00', 'cardio', 'Confirmed'),
(5, 'simbaa@gmail.com', '2025-01-20 09:30:00', 'cardio exercise', 'Pending'),
(6, 'apirajeya99@gmail.com', '2025-01-10 09:00:00', 'personal training premium', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `description`, `trainer_id`) VALUES
(4, 'dance', 'dance dance baby', 2),
(5, 'Yoga', 'Improve your flexibility, balance, and mental clarity with our calming yoga classes. \r\n                    Suitable for all levels, our classes promote peace and wellness.', 1),
(6, 'Zumba', 'Join the dance fitness revolution! Zumba classes combine fun dance moves with a high-energy workout to boost your cardio fitness.', 2),
(7, 'Pilates', 'Enhance your core strength and posture with Pilates. Our experienced instructors will guide you through precise movements to improve flexibility and muscle tone.', 3),
(8, 'Strength Training', 'Build muscle, strength, and power with our wide range of weight training options. Our expert trainers will guide you through effective workouts.', 4),
(9, 'song', 'songyy', 1),
(10, 'cardio exercise', 'Very effective', 3),
(13, 'cardio', 'helpful for body strength', 2),
(14, 'personal training premium', 'you will have personal trainer', 5);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `membership` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `class` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `firstName`, `lastName`, `email`, `phone`, `membership`, `password`, `class`) VALUES
(1, '', '', '', '', '', '$2y$10$wjevmg9940Ixu/Pyo9Ww/ecam6Ek32uO2dmLb.aG/R0NpiQC23We6', NULL),
(3, 'Apiramy', 'Jeayaharan', 'localhost@gmail.com', '0764652141', 'Premium Plan', '$2y$10$4ZNJuJR3qZdf243mobvNFun6.rTHPSQfR8mp1YHHoDWjgGrLdnOuu', NULL),
(4, 'Apira', 'Jeya', 'apiraarora@gmail.com', '0764652140', 'Premium Plan - 50K LKR/month', '$2y$10$AKW2ae0N8uL9Itj/uc0PYeLhlhrZHRAS.oFrMN98sfI8vlusYNlv.', 'Cardio Blast'),
(5, 'apira', 'hems', 'apirajeya99@gmail.com', '0764652141', 'Basic Plan - 20K LKR/month', '$2y$10$Nh6VlE5MR1NBRW.pazn3P.80peMUmRGDJabDQqKKs2d3DLABeNYzm', 'personal training premium'),
(7, 'simbaa', 'apira', 'apirasimbaa@gmail.com', '0764652141', 'Standard Plan', '$2y$10$ruPMK6I2pAV2VKSPdb30d.JA7oRE2jv8zn0sAthODKy9TZFw/KMiq', NULL),
(11, 'arthy', 'jeya', 'apiraarthy@gmail.com', '0764652141', 'Premium Plan', '$2y$10$PD5Mct9Oli.27N9S2ePqm.qnie5rYn26wNErfyXWw8E/g0fYhsgMm', NULL),
(12, 'sim', 'sank', 'sim@gmail.com', '0764652141', 'Premium Plan', '$2y$10$2.iS4CqQNEsM.9CgEZuLv.g8mjfAokI6fAZzsLYRJbiCvMKU1PCYm', NULL),
(13, 'raja', 'rasathi', 'raja@gmail.com', '09765678981', 'Premium Plan - 50K LKR/month', '$2y$10$f4yEqTMN0/dCIdizHa1BGO0dsA3iqkHTsDaKJ..XyOk3/Wpw1jYGa', NULL),
(14, 'heman', 'raje', 'hems@gmail.com', '0740022214', 'Basic Plan', '$2y$10$Qn8CN6RPc95KREiQW5gnRekd/uVKDzxFJy.mwwA1Hsuynziv7SGKu', NULL),
(15, 'heira', 'heman', 'heira@gmail.com', '0764652141', 'Basic Plan - 20K LKR/month', '$2y$10$MzBruIVxGEC9/f6IRuphzexG8Vb8D4zd3y6HLDVzvQuW37gWHqGLW', 'Yoga'),
(16, 'vetrii', 'rajee', 'vetri@gmail.com', '0745678976', 'semi luxury', '$2y$10$96hcW5G2rLh.C6ymyqWpP.Gafy08pVjtijseRmGt/jsB9OxAkG5nO', 'karate'),
(18, 'jisha', 'ambikai', 'jisha@gmail.com', '0764652141', 'premium', '$2y$10$WBGnAFg9pFjqYl2rnD2ANe34ZrPA9J5OdpNShp.beMauRAIO7d3Rq', 'Strength Training'),
(19, 'simbaa', 'mufasa', 'simbaa@gmail.com', '0764652149', 'Basic Plan - 20K LKR/month', '$2y$10$rA39nYkyEemj7shrDaOLDONJKwd8e6qhiWeEppeLM5A1mQb67IfJS', 'cardio exercise');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `membership_id` int(11) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `benefits` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`membership_id`, `plan_name`, `benefits`, `price`) VALUES
(2, 'personal', 'very well', 14500.00),
(3, 'premium', 'best option', 20000.00),
(5, '6 month plan', 'convenience', 28000.00),
(6, '9 month plan', 'you will get extra one month for free', 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `query` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response` text NOT NULL,
  `responded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queries`
--

INSERT INTO `queries` (`id`, `name`, `email`, `query`, `submitted_at`, `response`, `responded_at`) VALUES
(1, 'apira jeya', 'apiraarora@gmail.com', 'how are you', '2024-12-31 03:50:31', 'ok fine', '2025-01-01 23:05:49'),
(2, 'apira jeya', 'apiraarora@gmail.com', 'how are you buddy', '2024-12-31 08:36:44', 'bye bye', '2025-01-01 23:11:07'),
(3, 'apiramy', 'apirajeya99@gmail.com', 'i love you', '2025-01-02 18:17:18', 'i hate you', '2025-01-03 00:00:34'),
(4, 'Apira Jeya', 'apirasimbaa@gmail.com', 'will you marry me', '2025-01-02 18:18:40', 'nooo', '2025-01-08 09:11:30'),
(5, 'simbaa', 'raja@gmail.com', 'rajathi raja', '2025-01-02 18:21:42', '', NULL),
(6, 'vetri', 'vetri@gmail.com', 'jishaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-01-02 18:22:43', '', NULL),
(7, 'apira jeya', 'a18@gmail.com', 'do you do online classes', '2025-01-08 03:47:44', 'yes we will do', '2025-01-08 09:35:43'),
(8, 'raja', 'raja@gmail.com', 'can i meet you in person to get advise', '2025-01-08 04:08:58', 'yes you can', '2025-01-08 09:41:39');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `experience` int(11) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `class` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `name`, `experience`, `photo`, `class`) VALUES
(2, 'Heman', 3, 'Screenshot (8).png', 'Pilates'),
(5, 'vetri', 3, 'c.jpg', 'Strength Training'),
(6, 'Heira', 7, 't3.jpg', 'personal training premium');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `role`, `created_at`) VALUES
(3, 'janu', 'janu@gmail.com', 'admin', '2025-01-02 18:16:38'),
(4, 'muthu', 'muthu@gmail.com', 'admin', '2025-01-08 03:42:32'),
(5, 'ram', 'ram@gmail.com', 'member', '2025-01-08 04:07:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_email` (`client_email`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_name` (`class_name`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`membership_id`);

--
-- Indexes for table `queries`
--
ALTER TABLE `queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `queries`
--
ALTER TABLE `queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`client_email`) REFERENCES `clients` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
