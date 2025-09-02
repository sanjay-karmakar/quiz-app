-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 07:45 AM
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
-- Database: `quiz_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `qa_quiz_questions`
--

CREATE TABLE `qa_quiz_questions` (
  `id` int(11) NOT NULL,
  `question` text DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT 'Y=>Yes, N=>No',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_quiz_questions`
--

INSERT INTO `qa_quiz_questions` (`id`, `question`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Which planet is known as the \"Red Planet\"?', 'Y', '2025-08-08 20:45:42', NULL),
(2, 'In what year did World War II end?', 'Y', '2025-08-08 20:46:20', NULL),
(3, 'What is the longest river in the world?', 'Y', '2025-08-08 20:46:20', NULL),
(4, 'Who wrote the play \"Hamlet\"?', 'Y', '2025-08-08 20:46:43', NULL),
(5, 'What is the chemical symbol for gold?', 'Y', '2025-08-08 20:47:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `qa_quiz_questions_answers`
--

CREATE TABLE `qa_quiz_questions_answers` (
  `id` int(11) NOT NULL,
  `quiz_questions_id` int(11) DEFAULT NULL,
  `options` varchar(255) DEFAULT NULL,
  `is_correct` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'Y=>Yes, N=>No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_quiz_questions_answers`
--

INSERT INTO `qa_quiz_questions_answers` (`id`, `quiz_questions_id`, `options`, `is_correct`) VALUES
(1, 1, 'Venus', 'N'),
(2, 1, 'Mars', 'Y'),
(3, 1, 'Jupiter', 'N'),
(4, 1, 'Saturn', 'N'),
(5, 2, '1939', 'N'),
(6, 2, '1941', 'N'),
(7, 2, '1945', 'Y'),
(8, 2, '1950', 'N'),
(9, 3, 'Amazon', 'N'),
(10, 3, 'Nile', 'Y'),
(11, 3, 'Yangtze', 'N'),
(12, 3, 'Mississippi', 'N'),
(13, 4, 'Charles Dickens', 'N'),
(14, 4, 'William Shakespeare', 'Y'),
(15, 4, 'Jane Austen', 'N'),
(16, 4, 'Mark Twain', 'N'),
(17, 5, 'Ag', 'N'),
(18, 5, 'Au', 'Y'),
(19, 5, 'Fe', 'N'),
(20, 5, 'Hg', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `qa_quiz_submissions`
--

CREATE TABLE `qa_quiz_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `submission_status` enum('S','NS') NOT NULL DEFAULT 'NS' COMMENT 'S=>Submitted, NS=>Not Submitted',
  `total_question_answered` int(11) DEFAULT NULL,
  `total_correct_answer` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_quiz_submissions`
--

INSERT INTO `qa_quiz_submissions` (`id`, `user_id`, `answers`, `submission_status`, `total_question_answered`, `total_correct_answer`, `created_at`, `updated_at`) VALUES
(1, 2, '{\"question_1\":2,\"question_2\":7,\"question_3\":10,\"question_4\":16,\"question_5\":19}', 'S', 5, 3, '2025-08-10 22:46:29', NULL),
(2, 2, '{\"question_1\":2,\"question_2\":null,\"question_3\":null,\"question_4\":null,\"question_5\":null}', 'S', 1, 1, '2025-08-10 23:06:53', NULL),
(3, 2, '{\"question_1\":4,\"question_2\":7,\"question_3\":10,\"question_4\":15,\"question_5\":18}', 'S', 5, 3, '2025-08-10 23:08:03', NULL),
(4, 2, '{\"question_1\":2,\"question_2\":7,\"question_3\":null,\"question_4\":null,\"question_5\":18}', 'S', 3, 3, '2025-08-10 23:09:02', NULL),
(26, 3, '{\"question_1\":2,\"question_2\":7,\"question_3\":10,\"question_4\":16,\"question_5\":19}', 'S', 5, 3, '2025-08-10 23:15:50', NULL),
(27, 4, '{\"question_1\":1,\"question_2\":7,\"question_3\":10,\"question_4\":15,\"question_5\":20}', 'S', 5, 2, '2025-08-10 23:18:08', NULL),
(28, 5, '{\"question_1\":null,\"question_2\":7,\"question_3\":12,\"question_4\":null,\"question_5\":17}', 'S', 3, 1, '2025-08-10 23:30:27', NULL),
(29, 6, '{\"question_1\":1,\"question_2\":null,\"question_3\":10,\"question_4\":16,\"question_5\":20}', 'S', 4, 1, '2025-08-10 23:33:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `qa_users`
--

CREATE TABLE `qa_users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `usertype` enum('SA','A','U') NOT NULL DEFAULT 'U' COMMENT 'SA=>Super Admin, A=>Admin, U=>User',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qa_users`
--

INSERT INTO `qa_users` (`id`, `username`, `usertype`, `created_at`, `updated_at`) VALUES
(1, 'superadmin@mailinator.com', 'SA', '2025-08-08 18:09:31', NULL),
(2, 'user1@mailinator.com', 'U', '2025-08-08 18:09:48', NULL),
(3, 'user2@mailinator.com', 'U', '2025-08-11 09:51:52', NULL),
(4, 'user3@mailinator.com', 'U', '2025-08-11 09:51:52', NULL),
(5, 'user4@mailinator.com', 'U', '2025-08-11 09:52:07', NULL),
(6, 'user5@mailinator.com', 'U', '2025-08-11 09:52:07', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `qa_quiz_questions`
--
ALTER TABLE `qa_quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qa_quiz_questions_answers`
--
ALTER TABLE `qa_quiz_questions_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qa_quiz_submissions`
--
ALTER TABLE `qa_quiz_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qa_users`
--
ALTER TABLE `qa_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `qa_quiz_questions`
--
ALTER TABLE `qa_quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `qa_quiz_questions_answers`
--
ALTER TABLE `qa_quiz_questions_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `qa_quiz_submissions`
--
ALTER TABLE `qa_quiz_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `qa_users`
--
ALTER TABLE `qa_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
