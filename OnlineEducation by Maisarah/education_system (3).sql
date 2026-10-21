-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 19, 2026 at 02:52 PM
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
-- Database: `education_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `learning_videos`
--

CREATE TABLE `learning_videos` (
  `video_id` int(11) NOT NULL,
  `standard` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `chapter` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `video_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_videos`
--

INSERT INTO `learning_videos` (`video_id`, `standard`, `subject`, `chapter`, `title`, `video_file`) VALUES
(1, 'Standard 1', 'Science', 1, 'Living Things', 'SCIENCE1.mp4'),
(2, 'Standard 1', 'Science', 2, 'Water and Weather', 'SCIENCE2.mp4'),
(3, 'Standard 1', 'Science', 3, 'Forces and Magnets', 'SCIENCE3.mp4'),
(4, 'Standard 1', 'Math', 1, 'Addition Basics', 'MATH1.mp4'),
(5, 'Standard 1', 'Math', 2, 'Subtraction Practice', 'MATH2.mp4'),
(6, 'Standard 1', 'Math', 3, 'Multiplication', 'MATH3.mp4'),
(7, 'Standard 2', 'Mathematics', 1, 'Advanced Addition', 'MATH4.mp4'),
(8, 'Standard 3', 'Science', 1, 'Animals and living things', 'SCIENCE4.mp4');

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `progress_id` int(11) NOT NULL,
  `user_code` varchar(10) DEFAULT NULL,
  `standard` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `total_points` int(11) DEFAULT 0,
  `total_attempt` int(11) DEFAULT 0,
  `total_correct` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_progress`
--

INSERT INTO `student_progress` (`progress_id`, `user_code`, `standard`, `subject`, `total_points`, `total_attempt`, `total_correct`) VALUES
(1, 'S001', NULL, NULL, 10000, 26, 20),
(2, 'S002', NULL, NULL, 1000, 2, 2),
(3, 'S003', NULL, NULL, 500, 4, 1),
(4, 'I001', NULL, NULL, 500, 1, 1),
(5, 'S009', 'Standard 2', 'Science', 1000, 3, 2),
(6, 'S009', '', '', 1000, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_code` varchar(10) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bill_code` varchar(100) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `user_code`, `plan_name`, `amount`, `bill_code`, `payment_status`, `subscribed_at`) VALUES
(1, 'S001', 'Basic Plan', 10.00, 'SIM976995', 'Paid', '2026-06-18 11:50:43'),
(2, 'S002', 'Standard Plan', 20.00, 'SIM713689', 'Paid', '2026-06-18 11:52:37'),
(3, 'S004', 'Premium Plan', 30.00, 'SIM627592', 'Paid', '2026-06-18 11:56:14'),
(4, 'S003', 'Premium Plan', 30.00, 'SIM835116', 'Paid', '2026-06-18 12:03:27'),
(5, 'S005', 'Standard Plan', 20.00, 'SIM981709', 'Paid', '2026-06-18 12:15:49'),
(6, 'S006', 'Basic Plan', 10.00, 't7zgh38m', 'Paid', '2026-06-18 12:17:45'),
(7, 'S007', 'Premium Plan', 30.00, 'xqwzkzyf', 'Pending', '2026-06-18 12:26:25'),
(8, 'S008', 'Premium Plan', 30.00, 'rufdro8i', 'Pending', '2026-06-18 13:24:20'),
(9, 'S009', 'Standard Plan', 20.00, 'h8u6phb9', 'Paid', '2026-06-19 11:49:45'),
(10, 'S010', 'Basic Plan', 10.00, 'uvob96ly', 'Paid', '2026-06-19 12:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_code` varchar(10) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `standard` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT 'avatar.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject_taught` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_code`, `username`, `email`, `phone`, `role`, `standard`, `password`, `avatar`, `created_at`, `subject_taught`) VALUES
(2, 'S001', 'mai', 'mai@gmail.com', '08292182', 'Student', 'Standard 1', '$2y$10$JSAraUL9du53uLEeEs4v/OZjLsFir7mb6QHe.xrCCxpbyVwsdqo6C', 'avatar4.jpg', '2026-06-10 15:51:42', NULL),
(4, 'I001', 'haikal', 'haikal@instructor.OneStepAhead.com', '0171299861', 'Instructor', NULL, '$2y$10$nagdImbsnJVofK9.DuJf6uZYc4lakIroefVZ.aZDx6ESAV.oEp4Nq', 'avatar1.jpg', '2026-06-10 15:51:42', 'Science'),
(6, 'S002', 'ceja', 'ceja@gmail.com', '', 'Student', 'Standard 1', '$2y$10$FzNo2cmKd2oH/KjQzx/BvezAW8nomreAWHQQNqdTyTK1gSPSLVvSO', 'avatar.jpg', '2026-06-10 15:51:42', NULL),
(11, 'S003', 'aida', 'aida@gmail.com', '', 'Student', 'Standard 1', '$2y$10$yHDXLUCQHUFVtDofupd2ru2v9owuwguC8b07HtlRs4vC5.1fyoNny', 'avatar7.jpg', '2026-06-11 12:44:03', NULL),
(12, 'A001', 'Abu', 'abu@admin.OneStepAhead.com', '0123456789', 'Admin', NULL, 'abu123', 'avatar1.jpg', '2026-06-17 08:12:45', NULL),
(13, 'I003', 'Nurul Farah', 'nurul@instructor.OneStepAhead.com', '0123456789', 'Instructor', NULL, '$2y$10$Kdj6oGpLLIC8aNdjSNZLauYxORXKWZbCdh9Uc66zhZ0CF25DsGpxm', 'avatar2.jpg', '2026-06-17 08:23:53', 'Math'),
(14, 'I004', 'Alan Wang', 'alan@instructor.OneStepAhead.com', '0189666788', 'Instructor', NULL, '$2y$10$389.1dfHWeboFJpL0OgXSu9nV4DhCUdRI4waXB5ChTOHbq592W5vO', 'avatar3.jpg', '2026-06-18 03:42:50', 'Math'),
(16, 'S004', 'AinaPretty', 'aina@gmail.com', '', 'Student', 'Standard 1', '$2y$10$CY8d07palrmpgCPWm4OLrO3.pmrlknELfxaKXbwsUthUL1a7zRZmG', 'avatar.jpg', '2026-06-18 11:56:07', NULL),
(17, 'S005', 'Yeonjun', 'choi@gmail.com', '', 'Student', 'Standard 1', '$2y$10$/Tto/clFtvNhCbs5/G/XQuEUdqWZPeT4K7zfgClbKKfsHGN0uHt4u', 'avatar.jpg', '2026-06-18 12:15:42', NULL),
(18, 'S006', 'test', 'test@gmail.com', '', 'Student', 'Standard 1', '$2y$10$JGhVGm6UxC.gDYSm2TETNORnESO45HnChkfFIjoCHyv/7lh2.QeTi', 'avatar.jpg', '2026-06-18 12:17:40', NULL),
(19, 'S007', 'test123', 'test123@gmail.com', '', 'Student', 'Standard 1', '$2y$10$YJjtvb0KIVxIgTwJYwUnuu4mK.RkjIkOsrgn2ZBKX/JNgNG8q/78S', 'avatar.jpg', '2026-06-18 12:26:17', NULL),
(20, 'S008', 'abubakar@gmail.com', 'abubakar@gmail.com', '', 'Student', 'Standard 1', '$2y$10$4wU7WwtBME0fFck3OJuU1OC9BxLUvtswyUoO7kwTw5ZTgEFXBZIOa', 'avatar.jpg', '2026-06-18 13:24:12', NULL),
(21, 'S009', 'jack', 'jack@gmail.com', '', 'Student', 'Standard 1', '$2y$10$OyjVGn1uzwMIGHAxW8v9CuGRcJxN6OFKVrcS6MgpvCf3Ork4YKnwe', 'avatar.jpg', '2026-06-19 11:49:38', NULL),
(22, 'S010', 'Addy', 'Addy@gmail.com', '', 'Student', 'Standard 1', '$2y$10$B.RZqFOqSER0w5Yf0hElTemLHvGA6dI3NsV4IoX8/IfnO7J03SVX.', 'avatar.jpg', '2026-06-19 12:48:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `worksheet_questions`
--

CREATE TABLE `worksheet_questions` (
  `question_id` int(11) NOT NULL,
  `question_text` varchar(255) DEFAULT NULL,
  `option_a` varchar(100) DEFAULT NULL,
  `option_b` varchar(100) DEFAULT NULL,
  `option_c` varchar(100) DEFAULT NULL,
  `option_d` varchar(100) DEFAULT NULL,
  `correct_answer` char(1) DEFAULT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `standard` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `explanation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `worksheet_questions`
--

INSERT INTO `worksheet_questions` (`question_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `video_link`, `standard`, `subject`, `explanation`, `created_at`) VALUES
(5, '5 + x = 8', 'x = 3', 'x = 2', 'x = 5', 'x = 8', 'A', 'q1.mp4', 'Standard 1', 'Math', NULL, '2026-06-10 15:51:42'),
(6, '7 × 6 = x', 'x = 36', 'x = 42', 'x = 49', 'x = 70', 'B', 'q2.mp4', 'Standard 1', 'Math', NULL, '2026-06-10 15:51:42'),
(7, '100 + 2 - 10 = x', 'x = 100', 'x = 90', 'x = 82', 'x = 92', 'D', 'q3.mp4', 'Standard 2', 'Math', NULL, '2026-06-10 15:51:42'),
(8, 'Plants need ______ to make food.', 'Water', 'Sunlight', 'Soil', 'Air', 'B', 'science1.mp4', 'Standard 1', 'Science', NULL, '2026-06-10 15:51:42'),
(9, 'Which part of the body is used for seeing?', 'Ear', 'Nose', 'Eye', 'Hand', 'C', 'science2.mp4', 'Standard 1', 'Science', NULL, '2026-06-10 15:51:42'),
(10, 'Which part of a plant absorbs water from the soil?', 'Flower', 'Leaf', 'Root', 'Stem', 'C', NULL, 'Standard 2', 'Science', NULL, '2026-06-11 04:12:55'),
(11, '25 + 15 = ?', '30', '35', '40', '45', 'C', NULL, 'Standard 2', 'Mathematics', '			Add the tens and ones together. 25 + 15 = 40.', '2026-06-17 08:19:43'),
(12, '8 × 4 = ?', '24', '28', '32', '36', 'C', NULL, 'Standard 2', 'Mathematics', '			Multiplying 8 by 4 gives 32.', '2026-06-17 08:20:55'),
(13, 'Which number is the largest?', '76', '67', '79', '69', 'C', NULL, 'Standard 2', 'Mathematics', '			79 has the greatest value among all the numbers.', '2026-06-17 08:25:52'),
(14, 'Which animal lays eggs?', 'Cat', 'Cow', 'Rabbit', 'Chicken', 'D', NULL, 'Standard 2', 'Science', '			A chicken is an animal that lays eggs.', '2026-06-17 08:27:33'),
(15, 'The Sun gives us ______.', 'Rain', 'Heat and light', 'Wind', 'Soil', 'B', NULL, 'Standard 2', 'Science', '			The Sun provides heat and light to Earth.', '2026-06-17 08:29:02'),
(16, 'What is half of 20?', '5', '10', '15', '20', 'B', NULL, 'Standard 2', 'Math', '			Half means dividing by 2. 20 ÷ 2 = 10.', '2026-06-17 08:44:35'),
(17, 'Which number is the largest?', '67', '76', '79', '69', 'C', NULL, 'Standard 2', 'Math', '			79 has the greatest value among all the numbers.', '2026-06-17 08:45:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `learning_videos`
--
ALTER TABLE `learning_videos`
  ADD PRIMARY KEY (`video_id`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`progress_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `worksheet_questions`
--
ALTER TABLE `worksheet_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learning_videos`
--
ALTER TABLE `learning_videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `worksheet_questions`
--
ALTER TABLE `worksheet_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
