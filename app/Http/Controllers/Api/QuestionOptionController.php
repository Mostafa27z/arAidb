-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 03:11 PM
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
-- Database: `ailms`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_logs`
--

CREATE TABLE `ai_chat_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_ai` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `club_chat_messages`
--

CREATE TABLE `club_chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `club_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `club_members`
--

CREATE TABLE `club_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `club_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints_suggestions`
--

CREATE TABLE `complaints_suggestions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('complaint','suggestion') NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `exam_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `score` double NOT NULL,
  `total_marks` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started',
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_02_19_205538_create_personal_access_tokens_table', 1),
(5, '2025_03_13_084522_create_students_table', 1),
(6, '2025_03_13_084718_create_teachers_table', 1),
(7, '2025_03_13_084911_create_courses_table', 1),
(8, '2025_03_13_084947_create_lessons_table', 1),
(9, '2025_03_13_085142_create_lesson_progress_table', 1),
(10, '2025_03_13_085247_create_course_enrollments_table', 1),
(11, '2025_03_13_085404_create_groups_table', 1),
(12, '2025_03_13_085504_create_group_members_table', 1),
(13, '2025_03_13_085622_create_ai_chat_logs_table', 1),
(14, '2025_03_13_085736_create_complaints_suggestions_table', 1),
(15, '2025_03_13_085835_create_notifications_table', 1),
(16, '2025_03_13_085936_create_questions_table', 1),
(17, '2025_03_13_090037_create_question_options_table', 1),
(18, '2025_03_13_090129_create_student_answers_table', 1),
(19, '2025_03_13_090257_create_assignments_table', 1),
(20, '2025_03_13_090352_create_student_submissions_table', 1),
(21, '2025_03_13_090454_create_teacher_assignment_review_table', 1),
(22, '2025_03_13_090550_create_clubs_table', 1),
(23, '2025_03_13_090654_create_club_members_table', 1),
(24, '2025_03_13_090752_create_club_chat_messages_table', 1),
(25, '2025_03_13_090856_create_parent_teacher_conversations_table', 1),
(26, '2025_03_13_090953_create_parent_teacher_messages_table', 1),
(27, '2025_03_13_091039_create_parent_teacher_notifications_table', 1),
(28, '2025_03_13_091128_create_exams_table', 1),
(29, '2025_03_13_091223_create_exam_results_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_teacher_conversations`
--

CREATE TABLE `parent_teacher_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_teacher_messages`
--

CREATE TABLE `parent_teacher_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_teacher_notifications`
--

CREATE TABLE `parent_teacher_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `message_preview` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'api-token', '0c625bcba3d91b8bab229ba986243f8ce5162b9eca2930818bdc572267b44bea', '[\"*\"]', NULL, NULL, '2025-06-10 11:41:03', '2025-06-10 11:41:03'),
(2, 'App\\Models\\User', 1, 'api-token', '8daa69d756569fdf0d21caf11f7110d71fead717507dfad801daedc051071e93', '[\"*\"]', NULL, NULL, '2025-06-10 11:42:40', '2025-06-10 11:42:40'),
(3, 'App\\Models\\User', 2, 'api-token', 'c525e33130c071368fac65a6b4df9fef81057c428123b9cc7b0e4e63db6994ee', '[\"*\"]', NULL, NULL, '2025-06-10 12:18:26', '2025-06-10 12:18:26'),
(4, 'App\\Models\\User', 2, 'api-token', '82d3c77edeeb657c4e7c7d4cf4bdd54c3aea05eea4a70b44d08bdf2c7b6ac85a', '[\"*\"]', NULL, NULL, '2025-06-10 12:20:35', '2025-06-10 12:20:35'),
(5, 'App\\Models\\User', 2, 'api-token', '2da7bb12a01267de606eb4cbc3faa16102c1df010cfda1ec49855b8260257016', '[\"*\"]', NULL, NULL, '2025-06-11 08:58:52', '2025-06-11 08:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lesson_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','checkbox','true_false','short_answer','essay') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `parent_id`, `grade_level`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, '2025-06-10 11:41:03', '2025-06-10 11:41:03'),
(2, 1, NULL, NULL, '2025-06-10 11:41:03', '2025-06-10 11:41:03'),
(3, 2, NULL, NULL, '2025-06-10 12:18:26', '2025-06-10 12:18:26');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `selected_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `essay_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_submissions`
--

CREATE TABLE `student_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submission_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `qualification` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_assignment_review`
--

CREATE TABLE `teacher_assignment_review` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `feedback` text NOT NULL,
  `score` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('student','parent','teacher','admin','moderator') NOT NULL DEFAULT 'student',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'student', 'most', 'aa@a.com', NULL, '$2y$12$nCU8nJ421IXOHdxufcXi7uasCwUbM2tUSG8sH4sspa2djDeDAJjvq', NULL, '2025-06-10 11:41:03', '2025-06-10 11:41:03'),
(2, 'student', 'mostaa', 'a@a.com', NULL, '$2y$12$xgPQRRhQN4B00mQxNzgk4O6g/q5fK42iRNJJEjZ5Btq.rsuMRZSIa', NULL, '2025-06-10 12:18:26', '2025-06-10 12:18:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_chat_logs`
--
ALTER TABLE `ai_chat_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ai_chat_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignments_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clubs_name_unique` (`name`);

--
-- Indexes for table `club_chat_messages`
--
ALTER TABLE `club_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_chat_messages_club_id_foreign` (`club_id`),
  ADD KEY `club_chat_messages_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `club_members`
--
ALTER TABLE `club_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_members_student_id_foreign` (`student_id`),
  ADD KEY `club_members_club_id_foreign` (`club_id`);

--
-- Indexes for table `complaints_suggestions`
--
ALTER TABLE `complaints_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaints_suggestions_user_id_foreign` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_enrollments_student_id_foreign` (`student_id`),
  ADD KEY `course_enrollments_course_id_foreign` (`course_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_course_id_foreign` (`course_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_results_student_id_foreign` (`student_id`),
  ADD KEY `exam_results_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groups_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_members_student_id_foreign` (`student_id`),
  ADD KEY `group_members_group_id_foreign` (`group_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_course_id_foreign` (`course_id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_progress_student_id_foreign` (`student_id`),
  ADD KEY `lesson_progress_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `parent_teacher_conversations`
--
ALTER TABLE `parent_teacher_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_teacher_conversations_parent_id_foreign` (`parent_id`),
  ADD KEY `parent_teacher_conversations_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `parent_teacher_messages`
--
ALTER TABLE `parent_teacher_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_teacher_messages_conversation_id_foreign` (`conversation_id`),
  ADD KEY `parent_teacher_messages_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `parent_teacher_notifications`
--
ALTER TABLE `parent_teacher_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_teacher_notifications_recipient_id_foreign` (`recipient_id`),
  ADD KEY `parent_teacher_notifications_conversation_id_foreign` (`conversation_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_options_question_id_foreign` (`question_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_student_id_foreign` (`student_id`),
  ADD KEY `students_parent_id_index` (`parent_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_answers_student_id_foreign` (`student_id`),
  ADD KEY `student_answers_question_id_foreign` (`question_id`),
  ADD KEY `student_answers_selected_option_id_foreign` (`selected_option_id`);

--
-- Indexes for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_submissions_student_id_foreign` (`student_id`),
  ADD KEY `student_submissions_assignment_id_foreign` (`assignment_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_user_id_unique` (`user_id`);

--
-- Indexes for table `teacher_assignment_review`
--
ALTER TABLE `teacher_assignment_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_assignment_review_submission_id_foreign` (`submission_id`),
  ADD KEY `teacher_assignment_review_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_chat_logs`
--
ALTER TABLE `ai_chat_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `club_chat_messages`
--
ALTER TABLE `club_chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `club_members`
--
ALTER TABLE `club_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints_suggestions`
--
ALTER TABLE `complaints_suggestions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_teacher_conversations`
--
ALTER TABLE `parent_teacher_conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_teacher_messages`
--
ALTER TABLE `parent_teacher_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_teacher_notifications`
--
ALTER TABLE `parent_teacher_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_submissions`
--
ALTER TABLE `student_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teacher_assignment_review`
--
ALTER TABLE `teacher_assignment_review`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_chat_logs`
--
ALTER TABLE `ai_chat_logs`
  ADD CONSTRAINT `ai_chat_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Constraints for table `club_chat_messages`
--
ALTER TABLE `club_chat_messages`
  ADD CONSTRAINT `club_chat_messages_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `club_chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `club_members`
--
ALTER TABLE `club_members`
  ADD CONSTRAINT `club_members_club_id_foreign` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`id`),
  ADD CONSTRAINT `club_members_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `complaints_suggestions`
--
ALTER TABLE `complaints_suggestions`
  ADD CONSTRAINT `complaints_suggestions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `course_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`),
  ADD CONSTRAINT `exam_results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `group_members_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`),
  ADD CONSTRAINT `lesson_progress_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parent_teacher_conversations`
--
ALTER TABLE `parent_teacher_conversations`
  ADD CONSTRAINT `parent_teacher_conversations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `parent_teacher_conversations_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `parent_teacher_messages`
--
ALTER TABLE `parent_teacher_messages`
  ADD CONSTRAINT `parent_teacher_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `parent_teacher_conversations` (`id`),
  ADD CONSTRAINT `parent_teacher_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parent_teacher_notifications`
--
ALTER TABLE `parent_teacher_notifications`
  ADD CONSTRAINT `parent_teacher_notifications_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `parent_teacher_conversations` (`id`),
  ADD CONSTRAINT `parent_teacher_notifications_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_parent_user_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `student_answers_selected_option_id_foreign` FOREIGN KEY (`selected_option_id`) REFERENCES `question_options` (`id`),
  ADD CONSTRAINT `student_answers_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD CONSTRAINT `student_submissions_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`),
  ADD CONSTRAINT `student_submissions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `teacher_assignment_review`
--
ALTER TABLE `teacher_assignment_review`
  ADD CONSTRAINT `teacher_assignment_review_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `student_submissions` (`id`),
  ADD CONSTRAINT `teacher_assignment_review_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
