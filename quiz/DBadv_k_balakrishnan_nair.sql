-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 09, 2025 at 04:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adv_k_balakrishnan_nair`
--

-- --------------------------------------------------------

--
-- Table structure for table `globals`
--

CREATE TABLE `globals` (
  `global_id` bigint(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `globals`
--

INSERT INTO `globals` (`global_id`, `name`, `value`) VALUES
(1, 'domain', 'http://localhost/adv_k_balakrishnannair');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` bigint(100) NOT NULL,
  `quiz_id` bigint(100) NOT NULL,
  `question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(44, 5, 'test qn 1 ?', 'a1', 'b1', 'c1', 'd1', 'a'),
(45, 5, 'test qn 2 ?', 'a2', 'b2', 'c2', 'd2', 'b'),
(46, 6, 'Capital of India ?', 'Delhi', 'Trivandrum', 'Bombay', 'Kolkata', 'a'),
(47, 6, 'qn2', 'opt 1', 'opt 2', 'opt 3', 'opt 4', 'b');

-- --------------------------------------------------------

--
-- Table structure for table `question_answer_submissions`
--

CREATE TABLE `question_answer_submissions` (
  `question_answer_submission_id` bigint(100) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `quiz_id` bigint(100) NOT NULL,
  `submitted_answer` enum('a','b','c','d') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` bigint(100) NOT NULL,
  `quiz_name` varchar(50) NOT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT 0,
  `duration_in_minutes` int(5) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `stop_time` datetime DEFAULT NULL,
  `allowed_entry` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `quiz_name`, `is_running`, `duration_in_minutes`, `start_time`, `stop_time`, `allowed_entry`) VALUES
(5, 'Test Quiz 10', 1, 60, NULL, NULL, 0),
(6, 'Lexathon Test', 1, 30, '2025-01-09 17:06:25', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_submissions`
--

CREATE TABLE `quiz_submissions` (
  `quiz_submission_id` bigint(100) NOT NULL,
  `quiz_id` bigint(100) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `score` int(4) NOT NULL,
  `certificate_path` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','participant') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `college` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `ini_col_code` varchar(50) NOT NULL,
  `year_of_joining` year(4) NOT NULL,
  `programme` varchar(100) NOT NULL,
  `disqualified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `first_name`, `last_name`, `phone_no`, `college`, `city`, `state`, `country`, `pincode`, `graduation_year`, `ini_col_code`, `year_of_joining`, `programme`, `disqualified`) VALUES
(1, 'admin@advkbalakrishnannair.com', '$2y$10$SbVP28gO9nKzkesBSS.T/Oz.zbkAukSY2GCw.9ry1dGIiwmup73U2', 'admin', 'Admin', 'Admin', '9876543210', 'N.A', 'Trivandrum', 'Kerala', 'India', '695012', '2025', '', '0000', '', 0),
(2, 'student@test.com', '$2y$10$SbVP28gO9nKzkesBSS.T/Oz.zbkAukSY2GCw.9ry1dGIiwmup73U2', 'participant', 'Student', 'Student', '9876543210', 'test college', 'Trivandrum', 'Kerala', 'India', '695012', '2025', '', '0000', '', 0),
(3, 'stevesajanjacobkallunkal004@gmail.com', '$2y$10$JYVZ7BOrCVUMloirQeJiYeizEDVzDhwed1OaJUTzu7E5RbBMeARyW', 'participant', 'Steve Sajanj', 'Jacoba', '6238936249', 'CNja', 'Trivandruma', 'Keralaa', 'Indiaa', '695583', '2025', '335', '2023', 'BCAa', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `globals`
--
ALTER TABLE `globals`
  ADD PRIMARY KEY (`global_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_question_reference` (`quiz_id`);

--
-- Indexes for table `question_answer_submissions`
--
ALTER TABLE `question_answer_submissions`
  ADD PRIMARY KEY (`question_answer_submission_id`),
  ADD KEY `participant_qa_reference` (`participant_id`),
  ADD KEY `question_reference` (`question_id`),
  ADD KEY `quiz_reference` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD PRIMARY KEY (`quiz_submission_id`),
  ADD KEY `quiz_submission_reference` (`quiz_id`),
  ADD KEY `participant_submission_reference` (`participant_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `globals`
--
ALTER TABLE `globals`
  MODIFY `global_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `question_answer_submissions`
--
ALTER TABLE `question_answer_submissions`
  MODIFY `question_answer_submission_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `quiz_submission_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `quiz_question_reference` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_answer_submissions`
--
ALTER TABLE `question_answer_submissions`
  ADD CONSTRAINT `participant_qa_reference` FOREIGN KEY (`participant_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `question_reference` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quiz_reference` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD CONSTRAINT `participant_submission_reference` FOREIGN KEY (`participant_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `quiz_submission_reference` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
