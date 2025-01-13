-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2025 at 11:58 AM
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
(1, 'domain', 'http://192.168.31.33/adv_k_balakrishnannair');

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
(57, 10, 'Capital of UAE ?', 'Trivandrum', 'Delhi', 'Guahati', 'Abu Dhabi', 'd'),
(58, 10, 'Capital of Russia', 'Delhi', 'Moscow', 'Guahati', 'Bombay', 'b'),
(59, 11, 'test qn 1', 'a', 'b', 'c', 'd', 'a'),
(60, 11, 'test qn2', 'Kochi', 'Trivandrum', 'Kollam', 'Thrissur', 'd');

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

--
-- Dumping data for table `question_answer_submissions`
--

INSERT INTO `question_answer_submissions` (`question_answer_submission_id`, `question_id`, `participant_id`, `quiz_id`, `submitted_answer`) VALUES
(53, 59, 2, 11, 'a'),
(54, 60, 2, 11, 'd');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` bigint(100) NOT NULL,
  `quiz_name` varchar(50) NOT NULL,
  `duration_in_minutes` int(5) NOT NULL,
  `total_marks` int(5) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `stop_time` datetime DEFAULT NULL,
  `allowed_entry` tinyint(1) NOT NULL DEFAULT 0,
  `is_conducted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `quiz_name`, `duration_in_minutes`, `total_marks`, `start_time`, `stop_time`, `allowed_entry`, `is_conducted`) VALUES
(10, 'Test Quiz 6', 21, 2, '2025-01-11 14:21:47', '2025-01-11 14:52:47', 1, 0),
(11, 'Test Quiz 7', 20, 2, '2025-01-13 10:38:53', '2025-01-13 11:08:53', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_submissions`
--

CREATE TABLE `quiz_submissions` (
  `quiz_submission_id` bigint(100) NOT NULL,
  `quiz_id` bigint(100) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `quiz_start_time` datetime NOT NULL,
  `quiz_submission_time` datetime DEFAULT NULL,
  `score` int(4) DEFAULT NULL,
  `disqualified_submission` tinyint(1) DEFAULT 0,
  `disqualification_reason` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_submissions`
--

INSERT INTO `quiz_submissions` (`quiz_submission_id`, `quiz_id`, `participant_id`, `quiz_start_time`, `quiz_submission_time`, `score`, `disqualified_submission`, `disqualification_reason`) VALUES
(59, 11, 2, '2025-01-13 10:46:02', '2025-01-13 10:46:08', 2, 0, NULL);

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
  `disqualified` tinyint(1) NOT NULL DEFAULT 0,
  `disqualification_reason` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `first_name`, `last_name`, `phone_no`, `college`, `city`, `state`, `country`, `pincode`, `graduation_year`, `ini_col_code`, `year_of_joining`, `programme`, `disqualified`, `disqualification_reason`) VALUES
(1, 'admin@advkbalakrishnannair.com', '$2y$10$SbVP28gO9nKzkesBSS.T/Oz.zbkAukSY2GCw.9ry1dGIiwmup73U2', 'admin', 'Admin', 'Admin', '9876543210', 'N.A', 'Trivandrum', 'Kerala', 'India', '695012', '2025', '', '0000', '', 0, NULL),
(2, 'student@test.com', '$2y$10$SbVP28gO9nKzkesBSS.T/Oz.zbkAukSY2GCw.9ry1dGIiwmup73U2', 'participant', 'Student', 'Student', '9876543210', 'test college', 'Trivandrum', 'Kerala', 'India', '695012', '2025', '', '0000', '', 0, NULL),
(3, 'stevesajanjacobkallunkal004@gmail.com', '$2y$10$JYVZ7BOrCVUMloirQeJiYeizEDVzDhwed1OaJUTzu7E5RbBMeARyW', 'participant', 'Steve Sajanj', 'Jacoba', '6238936249', 'CNja', 'Trivandruma', 'Keralaa', 'Indiaa', '695583', '2025', '335', '2023', 'BCAa', 0, NULL);

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
  MODIFY `question_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `question_answer_submissions`
--
ALTER TABLE `question_answer_submissions`
  MODIFY `question_answer_submission_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `quiz_submission_id` bigint(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

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
