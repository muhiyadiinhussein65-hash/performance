-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 08:49 AM
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
-- Database: `student`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `providerAccountId` varchar(255) NOT NULL,
  `accessToken` varchar(255) DEFAULT NULL,
  `refreshToken` varchar(255) DEFAULT NULL,
  `expiresAt` int(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `criteria_text` text NOT NULL,
  `score_reward` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `chapter_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `subject_id`, `title`, `description`, `created_by_user_id`, `created_at`, `chapter_number`) VALUES
(1, 15, 'chapter One', 'hello', NULL, '2025-10-12 18:38:49', 1),
(2, 11, 'chapter one', 'hello', NULL, '2025-10-12 18:38:49', 1),
(3, 1, 'chapter one', 'chapter one ', NULL, '2025-10-13 18:11:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `daily_activity_log`
--

CREATE TABLE `daily_activity_log` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `activity_date` date NOT NULL,
  `points_earned` int(11) DEFAULT 0,
  `questions_answered` int(11) DEFAULT 0,
  `minutes_spent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaderboards`
--

CREATE TABLE `leaderboards` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `rank_position` int(11) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `summary_id` int(11) NOT NULL,
  `question_type` enum('direct','multiple_choice','true_false') NOT NULL,
  `question_text` text NOT NULL,
  `difficulty_level` enum('easy','medium','hard') DEFAULT 'medium',
  `estimated_time_seconds` int(11) DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `summary_id`, `question_type`, `question_text`, `difficulty_level`, `estimated_time_seconds`, `created_by_user_id`, `created_at`, `points`) VALUES
(1, 1, 'multiple_choice', 'What is the capital of France?', 'easy', 30, NULL, '2025-10-14 17:45:00', NULL),
(2, 1, 'true_false', 'The Earth is the third planet from the Sun.', 'easy', 20, NULL, '2025-10-14 17:45:00', NULL),
(3, 1, 'direct', 'What is the chemical symbol for gold?', 'medium', 25, NULL, '2025-10-14 17:45:00', NULL),
(4, 1, 'multiple_choice', 'Which of the following is NOT a primary color of light?', 'medium', 35, NULL, '2025-10-14 17:45:00', NULL),
(5, 1, 'true_false', 'Shakespeare wrote \"Romeo and Juliet\" in the 18th century.', 'medium', 25, NULL, '2025-10-14 17:45:00', NULL),
(6, 1, 'direct', 'What is the value of Pi rounded to five decimal places?', 'hard', 40, NULL, '2025-10-14 17:45:00', NULL),
(7, 1, 'multiple_choice', 'Which philosopher is known for the statement \"I think, therefore I am\"?', 'hard', 45, NULL, '2025-10-14 17:45:00', NULL),
(8, 1, 'true_false', 'The Great Wall of China is visible from space with the naked eye.', 'hard', 30, NULL, '2025-10-14 17:45:01', NULL),
(9, 1, 'direct', 'How many continents are there on Earth?', 'easy', 20, NULL, '2025-10-14 17:45:01', NULL),
(10, 1, 'multiple_choice', 'Which programming language is known as the \"language of the web\"?', 'medium', 35, NULL, '2025-10-14 17:45:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `question_answers`
--

CREATE TABLE `question_answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `correct_answer_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_answers`
--

INSERT INTO `question_answers` (`id`, `question_id`, `correct_answer_text`) VALUES
(1, 3, 'Au'),
(2, 6, '3.14159'),
(3, 9, '7');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'London', 0),
(2, 1, 'Paris', 1),
(3, 1, 'Berlin', 0),
(4, 1, 'Madrid', 0),
(5, 2, 'True', 1),
(6, 2, 'False', 0),
(7, 4, 'Red', 0),
(8, 4, 'Green', 0),
(9, 4, 'Blue', 0),
(10, 4, 'Yellow', 1),
(11, 5, 'True', 0),
(12, 5, 'False', 1),
(13, 7, 'Plato', 0),
(14, 7, 'Aristotle', 0),
(15, 7, 'René Descartes', 1),
(16, 7, 'Immanuel Kant', 0),
(17, 8, 'True', 0),
(18, 8, 'False', 1),
(19, 10, 'Python', 0),
(20, 10, 'Java', 0),
(21, 10, 'JavaScript', 1),
(22, 10, 'C++', 0);

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `expiresAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `school` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone_number`, `address`, `school_id`, `created_at`, `school`) VALUES
(1, 'xasan xuseen', 'xasana', '4541822', 'borama', NULL, '2025-10-15 19:41:50', 'adan isak'),
(2, 'xasan', NULL, NULL, 'borama', NULL, '2025-10-18 08:58:56', 'adan isak'),
(3, 'xasan', NULL, NULL, 'Jabuuti', NULL, '2025-10-18 09:21:06', 'adan isak'),
(4, 'xasan Xuseen', NULL, NULL, 'Fardaha', NULL, '2025-10-25 16:18:07', 'Waaberi');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `ai_feedback_json` text DEFAULT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `student_id`, `question_id`, `answer_text`, `option_id`, `is_correct`, `points_earned`, `ai_feedback_json`, `answered_at`) VALUES
(22, 1, 3, 'test answer', NULL, 1, 10, NULL, '2025-10-15 19:54:28'),
(25, 1, 7, NULL, 15, 1, 10, NULL, '2025-10-15 19:57:32'),
(26, 1, 4, NULL, 8, 0, 0, NULL, '2025-10-15 19:57:57'),
(27, 1, 7, NULL, 13, 0, 0, NULL, '2025-10-18 18:37:37'),
(28, 1, 10, NULL, 19, 0, 0, NULL, '2025-10-18 18:37:52'),
(29, 1, 1, NULL, 2, 1, 10, NULL, '2025-10-18 18:38:10'),
(30, 1, 4, NULL, 8, 0, 0, NULL, '2025-10-18 18:38:16'),
(31, 1, 3, 'au', NULL, 1, 10, NULL, '2025-10-18 18:38:23'),
(32, 3, 7, NULL, 13, 0, 0, NULL, '2025-10-19 18:10:15'),
(33, 3, 7, NULL, 15, 1, 10, NULL, '2025-10-19 18:10:23'),
(34, 3, 7, NULL, 13, 0, 0, NULL, '2025-10-19 18:17:48'),
(35, 3, 1, NULL, 3, 0, 0, NULL, '2025-10-19 18:24:01'),
(36, 3, 7, NULL, 13, 0, 0, NULL, '2025-10-19 18:24:49'),
(37, 1, 1, NULL, 1, 0, 0, NULL, '2025-10-19 18:33:24'),
(38, 1, 4, NULL, 7, 0, 0, NULL, '2025-10-19 19:14:46'),
(39, 1, 7, NULL, 13, 0, 0, NULL, '2025-10-19 19:15:24'),
(40, 1, 7, NULL, 13, 0, 0, NULL, '2025-10-19 19:18:05'),
(41, 1, 1, NULL, 2, 1, 10, NULL, '2025-10-19 19:18:14'),
(42, 1, 4, NULL, 9, 0, 0, NULL, '2025-10-19 19:18:26'),
(43, 1, 10, NULL, 21, 1, 10, NULL, '2025-10-19 19:18:41'),
(44, 1, 9, '7', NULL, 1, 10, NULL, '2025-10-19 19:18:49'),
(45, 1, 3, 'au', NULL, 1, 10, NULL, '2025-10-19 19:18:54'),
(46, 1, 6, '3.14', NULL, 0, 0, NULL, '2025-10-19 19:19:03'),
(47, 1, 2, NULL, NULL, 1, 10, NULL, '2025-10-19 19:19:12'),
(48, 1, 5, NULL, NULL, 1, 10, NULL, '2025-10-19 19:19:16'),
(49, 1, 8, NULL, NULL, 1, 10, NULL, '2025-10-19 19:19:22'),
(50, 1, 4, NULL, 10, 1, 10, NULL, '2025-10-19 19:59:04'),
(51, 1, 7, NULL, 13, 0, 0, NULL, '2025-10-19 19:59:31'),
(52, 1, 1, NULL, 2, 1, 10, NULL, '2025-10-19 20:00:24'),
(53, 1, 10, NULL, 21, 1, 10, NULL, '2025-10-19 20:00:51'),
(54, 1, 9, '7', NULL, 1, 10, NULL, '2025-10-19 20:00:57'),
(55, 1, 6, '3.14157', NULL, 0, 0, NULL, '2025-10-19 20:01:07'),
(56, 1, 3, 'au', NULL, 1, 10, NULL, '2025-10-19 20:01:15'),
(57, 1, 5, NULL, NULL, 1, 10, NULL, '2025-10-19 20:01:20'),
(58, 1, 2, NULL, NULL, 1, 10, NULL, '2025-10-19 20:01:26'),
(59, 1, 8, NULL, NULL, 1, 10, NULL, '2025-10-19 20:01:33'),
(60, 1, 10, NULL, 21, 1, 10, NULL, '2025-10-20 15:28:17'),
(61, 1, 4, NULL, 10, 1, 10, NULL, '2025-10-20 15:28:31'),
(62, 1, 1, NULL, 2, 1, 10, NULL, '2025-10-20 15:28:38'),
(63, 1, 7, NULL, 13, 0, 0, NULL, '2025-10-20 15:28:50'),
(64, 1, 3, 'au', NULL, 1, 10, NULL, '2025-10-20 15:29:10'),
(65, 1, 6, '3.145900', NULL, 0, 0, NULL, '2025-10-20 15:29:32'),
(66, 1, 9, '7', NULL, 1, 10, NULL, '2025-10-20 15:29:48'),
(67, 1, 2, NULL, NULL, 1, 10, NULL, '2025-10-20 15:30:03'),
(68, 1, 5, NULL, NULL, 1, 10, NULL, '2025-10-20 15:30:08'),
(69, 1, 8, NULL, NULL, 1, 10, NULL, '2025-10-20 15:30:12'),
(70, 1, 4, NULL, 7, 0, 0, NULL, '2025-10-25 18:10:19'),
(71, 1, 7, NULL, 15, 1, 10, NULL, '2025-10-25 18:10:30');

-- --------------------------------------------------------

--
-- Table structure for table `student_badges`
--

CREATE TABLE `student_badges` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `earned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_progress`
--

CREATE TABLE `student_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_score` int(11) DEFAULT 0,
  `streak_days` int(11) DEFAULT 0,
  `last_activity` date DEFAULT NULL,
  `level` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_summary_mastery`
--

CREATE TABLE `student_summary_mastery` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `summary_id` int(11) NOT NULL,
  `mastery_level` decimal(5,2) DEFAULT 0.00,
  `correct_attempts` int(11) DEFAULT 0,
  `total_attempts` int(11) DEFAULT 0,
  `last_attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `icon` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `created_by_user_id`, `created_at`, `icon`) VALUES
(1, 'Mathematics', 'Advanced study of algebra, geometry, and calculus for scientific reasoning.', NULL, '2025-10-08 17:55:53', 'Calculator'),
(2, 'Physics', 'Study of matter, energy, motion, and the laws governing the universe.', NULL, '2025-10-08 17:55:53', 'Atom'),
(3, 'Chemistry', 'Exploration of elements, compounds, reactions, and laboratory practices.', NULL, '2025-10-08 17:55:53', 'FlaskRound'),
(4, 'Biology', 'Study of living organisms, anatomy, genetics, and ecosystems.', NULL, '2025-10-08 17:55:53', 'Microscope'),
(5, 'English Language', 'Focus on grammar, writing, and communication skills.', NULL, '2025-10-08 17:55:53', 'BookOpen'),
(6, 'Somali Language', 'Study of Somali grammar, literature, and composition.', NULL, '2025-10-08 17:55:53', 'Book'),
(7, 'Islamic Studies', 'Learning Islamic beliefs, practices, and history.', NULL, '2025-10-08 17:55:53', 'MoonStar'),
(8, 'Mathematics', 'Advanced study of algebra, geometry, and calculus for scientific reasoning.', NULL, '2025-10-12 17:59:08', 'SquareRoot'),
(9, 'Physics', 'Study of matter, energy, motion, and the laws governing the universe.', NULL, '2025-10-12 17:59:08', 'Atom'),
(10, 'Chemistry', 'Exploration of elements, compounds, reactions, and laboratory practices.', NULL, '2025-10-12 17:59:08', 'FlaskRound'),
(11, 'Biology', 'Study of living organisms, anatomy, genetics, and ecosystems.', NULL, '2025-10-12 17:59:08', 'Dna'),
(12, 'English', 'Focus on grammar, writing, and communication skills.', NULL, '2025-10-12 17:59:08', 'Feather'),
(13, 'Geography', 'Study of the Earth, its landscapes, environments, and the relationships between people and their environments.', NULL, '2025-10-12 17:59:08', 'MapMarkedAlt'),
(14, 'History', 'Study of past events, societies, and cultures.', NULL, '2025-10-12 17:59:08', 'University'),
(15, 'Arabic', 'Study of Arabic grammar, literature, and composition.', NULL, '2025-10-12 17:59:08', 'Scroll'),
(16, 'Somali', 'Study of Somali grammar, literature, and composition.', NULL, '2025-10-12 17:59:08', 'StarOfAfrica'),
(17, 'Islamic', 'Learning Islamic beliefs, practices, and history.', NULL, '2025-10-12 17:59:08', 'Mosque');

-- --------------------------------------------------------

--
-- Table structure for table `summaries`
--

CREATE TABLE `summaries` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `version_number` int(11) DEFAULT 1,
  `created_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reading_time` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `summaries`
--

INSERT INTO `summaries` (`id`, `chapter_id`, `title`, `content`, `version_number`, `created_by_user_id`, `created_at`, `reading_time`) VALUES
(1, 3, 'algebra', 'Fixed PHP Errors:\n\nAdded proper fetch_assoc() calls for both summary and subject queries\n\nFixed variable naming consistency\n\nAdded question count query to show actual data\n\nEnhanced Mobile Responsiveness:\n\nAdded responsive text sizes with text-xs sm:text-sm\n\nResponsive padding and margins\n\nTouch-friendly button with min-height: 48px\n\nProper viewport meta tag\n\nImproved Layout:\n\nBetter content container with container-mobile\n\nContent area in a visually distinct card\n\nResponsive icon sizes\n\nTruncated header title for long subject names\n\nTouch Interactions:\n\nAdded active:scale-95 for button feedback\n\nTouch event handlers for cards\n\nPrevented double-tap zoom\n\nSmooth transitions\n\nDynamic Data:\n\nActual question count from database\n\nDynamic reading time display\n\nProper parameter passing to quiz page\n\nAccessibility:\n\nProper semantic HTML\n\nAdequate touch targets\n\nColor contrast considerations\n\nBack button functionality', 1, NULL, '2025-10-14 14:10:34', '10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `emailVerified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `email`, `emailVerified`, `password_hash`, `role`, `school_id`, `created_at`, `updated_at`) VALUES
(1, 'xasan', 'muhiyadin', '', '2025-10-18 08:58:56', '$2y$10$I.NazUAw/ESTBf0tawfDM.wM4BCbvYPiDNvqgXMDBEvpaI4Kc2L1u', 'student', NULL, '2025-10-18 08:58:56', '2025-10-18 08:58:56'),
(3, 'xasan', 'muhiyadin123', '', '2025-10-18 09:21:06', '$2y$10$B03Al61tSz/YrffdUbXMjO.BRqYgXCx8H3EScOjD8DxcoMcIWtaEW', 'student', NULL, '2025-10-18 09:21:06', '2025-10-18 09:21:06'),
(4, 'xasan Xuseen', 'xasan!@#', '', '2025-10-25 16:18:07', '$2y$10$4MvjAsUg1.Cyyas2tqEX0OhnyWq5nwfmQklKfwaIjP6OeU774Fdzu', 'student', NULL, '2025-10-25 16:18:07', '2025-10-25 16:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `verificationtokens`
--

CREATE TABLE `verificationtokens` (
  `id` int(11) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiresAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `badges_name_unique` (`name`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapters_subject_id_subjects_id_fk` (`subject_id`),
  ADD KEY `chapters_created_by_user_id_user_id_fk` (`created_by_user_id`);

--
-- Indexes for table `daily_activity_log`
--
ALTER TABLE `daily_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_student_date` (`student_id`,`activity_date`);

--
-- Indexes for table `leaderboards`
--
ALTER TABLE `leaderboards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaderboards_student_id_students_id_fk` (`student_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_created_by_user_id_user_id_fk` (`created_by_user_id`),
  ADD KEY `idx_summary_id` (`summary_id`),
  ADD KEY `idx_question_type` (`question_type`),
  ADD KEY `idx_summary_type` (`summary_id`,`question_type`);

--
-- Indexes for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_answers_question_id_unique` (`question_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_email_unique` (`email`),
  ADD KEY `students_school_id_schools_id_fk` (`school_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_answers_student_id_students_id_fk` (`student_id`),
  ADD KEY `student_answers_question_id_questions_id_fk` (`question_id`),
  ADD KEY `student_answers_option_id_question_options_id_fk` (`option_id`);

--
-- Indexes for table `student_badges`
--
ALTER TABLE `student_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_student_badge` (`student_id`,`badge_id`),
  ADD KEY `student_badges_badge_id_badges_id_fk` (`badge_id`);

--
-- Indexes for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_progress_student_id_unique` (`student_id`);

--
-- Indexes for table `student_summary_mastery`
--
ALTER TABLE `student_summary_mastery`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_student_summary` (`student_id`,`summary_id`),
  ADD KEY `student_summary_mastery_summary_id_summaries_id_fk` (`summary_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subjects_created_by_user_id_user_id_fk` (`created_by_user_id`);

--
-- Indexes for table `summaries`
--
ALTER TABLE `summaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `summaries_chapter_id_chapters_id_fk` (`chapter_id`),
  ADD KEY `summaries_created_by_user_id_user_id_fk` (`created_by_user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_school_id_schools_id_fk` (`school_id`);

--
-- Indexes for table `verificationtokens`
--
ALTER TABLE `verificationtokens`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `daily_activity_log`
--
ALTER TABLE `daily_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaderboards`
--
ALTER TABLE `leaderboards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `question_answers`
--
ALTER TABLE `question_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `student_badges`
--
ALTER TABLE `student_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_summary_mastery`
--
ALTER TABLE `student_summary_mastery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `summaries`
--
ALTER TABLE `summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `verificationtokens`
--
ALTER TABLE `verificationtokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_created_by_user_id_user_id_fk` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `chapters_subject_id_subjects_id_fk` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `daily_activity_log`
--
ALTER TABLE `daily_activity_log`
  ADD CONSTRAINT `daily_activity_log_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `leaderboards`
--
ALTER TABLE `leaderboards`
  ADD CONSTRAINT `leaderboards_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_created_by_user_id_user_id_fk` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `questions_summary_id_summaries_id_fk` FOREIGN KEY (`summary_id`) REFERENCES `summaries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD CONSTRAINT `question_answers_question_id_questions_id_fk` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_question_id_questions_id_fk` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_school_id_schools_id_fk` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_option_id_question_options_id_fk` FOREIGN KEY (`option_id`) REFERENCES `question_options` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_answers_question_id_questions_id_fk` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_answers_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `student_badges`
--
ALTER TABLE `student_badges`
  ADD CONSTRAINT `student_badges_badge_id_badges_id_fk` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_badges_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `student_summary_mastery`
--
ALTER TABLE `student_summary_mastery`
  ADD CONSTRAINT `student_summary_mastery_student_id_students_id_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_summary_mastery_summary_id_summaries_id_fk` FOREIGN KEY (`summary_id`) REFERENCES `summaries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_created_by_user_id_user_id_fk` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `summaries`
--
ALTER TABLE `summaries`
  ADD CONSTRAINT `summaries_chapter_id_chapters_id_fk` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `summaries_created_by_user_id_user_id_fk` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_school_id_schools_id_fk` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
