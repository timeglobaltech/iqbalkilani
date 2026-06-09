-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2026 at 09:42 AM
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
-- Database: `islamic_scholar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$mFgnHtbuM9/zjc38BMxOhepgeAMde.Tdo9WdoNiq2/5O8FxdG0fCC', 'admin', '2026-04-29 07:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `date_published` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `excerpt`, `content`, `category`, `date_published`, `created_at`) VALUES
(1, 'Understanding Tawakkul in Times of Uncertainty', 'understanding-tawakkul', 'A brief overview of relying on Allah during difficult times.', 'Full content goes here...', 'Spirituality', '2023-05-10', '2026-04-29 07:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `audios`
--

CREATE TABLE `audios` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `audio_url` varchar(255) NOT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audios`
--

INSERT INTO `audios` (`id`, `title`, `description`, `audio_url`, `duration`, `created_at`) VALUES
(26, 'Gustakhane Rasool Ka Jawab Kaise Den  || Muhammad Iqbal Kilani  ', '', 'uploads/audios/1778566314_5c611aab7447c60c.mp3', '', '2026-05-12 06:11:54'),
(28, 'Ghar Walo Ki Tarbiyat || Muhammad Iqbal Kilani', '', 'uploads/audios/1778566423_fe6d9c5e5e06a8ba.mp3', '', '2026-05-12 06:13:43'),
(29, 'Deen Ko Maanne Ke Fayede aur Na Maanne Ke Nuksaanat  || Muhammad Iqbal Kilani', '', 'uploads/audios/1778566497_7516eb48b453c5c0.mp3', '', '2026-05-12 06:14:57'),
(30, 'Amal Ke Qabul Hone Ki Sharten  || Muhammad Iqbal kilani', '', 'uploads/audios/1778566976_940c89c79c5903ae.mp3', '', '2026-05-12 06:22:56'),
(31, 'Duniya Aur Aakhirat Me Kaamiyaabi Kaise Mumkin Hai || Muhammad Iqbal Kilani', '', 'uploads/audios/1778567044_04d7f90237a9326f.mp3', '', '2026-05-12 06:24:04');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_urdu` varchar(255) DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `download_url` varchar(255) DEFAULT NULL,
  `language` varchar(50) DEFAULT 'Urdu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_image` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `is_free` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `title_urdu`, `cover_image_path`, `download_url`, `language`, `created_at`, `cover_image`, `file_path`, `price`, `is_free`) VALUES
(16, ' Description of Hell (Jahannam)', 'جہنم کا بیان', NULL, NULL, 'english', '2026-05-11 07:48:49', 'uploads/covers/1778485729_c825c7d89d73c51f.webp', '', 0.00, 0),
(17, 'Description of the Grave (Qabr)', 'قبر کا بیان', NULL, NULL, 'english', '2026-05-11 07:54:08', 'uploads/covers/1778486048_9ad27a603f2e0982.webp', NULL, 200.00, 0),
(18, 'Virtues of Companions — Part 1', 'فضائلِ صحابہ کرام (حصہ اول)', NULL, NULL, 'english', '2026-05-11 07:57:33', 'uploads/covers/1778486253_c84dec03e8a67f0d.webp', NULL, 0.00, 0),
(19, 'Virtues of Companions — Part 2', 'فضائلِ صحابہ کرام (حصہ دوم)', NULL, NULL, 'english', '2026-05-11 07:58:31', 'uploads/covers/1778486311_52fba8873a36a4df.webp', '', 0.00, 0),
(20, 'Virtues of Companions — Part 3', 'فضائلِ صحابہ کرام(حصہ سوم)', NULL, NULL, 'english', '2026-05-11 07:59:29', 'uploads/covers/1778486369_6fd7edfa16e8bc16.webp', '', 0.00, 0),
(21, 'IssueIssues of Zakats of Jihad', 'زکوٰۃ کے مسائل', NULL, NULL, 'urdu', '2026-05-11 09:24:08', 'uploads/covers/1778491448_a5c25549d3318b4e.webp', NULL, 100.00, 0),
(22, 'Issues of Medicine & Health (Tibb)', 'طب کے مسائل', NULL, NULL, 'urdu', '2026-05-11 09:27:06', 'uploads/covers/1778491626_7bbc1e2eacb06b64.jpg', NULL, 0.00, 0),
(23, 'Issues of Divorce (Talaq)', 'طلاق کے مسائل', NULL, NULL, 'urdu', '2026-05-11 09:28:57', 'uploads/covers/1778491737_232210700980a8e7.webp', NULL, 100.00, 0),
(24, 'Issues of Purification (Taharah)', 'طہارت کے مسائل', NULL, NULL, 'english', '2026-05-11 09:31:13', 'uploads/covers/1778491873_a61394fb0843c09c.webp', NULL, 100.00, 0),
(25, 'Description of Day of Judgment', 'قیامت کا بیان', NULL, NULL, 'urdu', '2026-05-11 09:32:21', 'uploads/covers/1778491941_91cdeffcfe1a1d63.webp', NULL, 0.00, 0),
(26, 'Issues of Marriage (Nikah)', 'نکاح کے مسائل', NULL, NULL, 'urdu', '2026-05-11 09:33:21', 'uploads/covers/1778492001_7bd503696d161b28.webp', NULL, 0.00, 0),
(27, 'Interest & Worldly Matters', 'سودی اور دنیوی', NULL, NULL, 'urdu', '2026-05-11 09:54:52', NULL, NULL, 100.00, 0),
(28, 'Teachings of the Holy Quran', 'تعلیماتِ قرآن مجید', NULL, NULL, 'urdu', '2026-05-11 09:58:24', NULL, NULL, 100.00, 0),
(29, 'Battles of Prophet ﷺ — Part 1', 'غزوات کا بیان (حصہ اول)', NULL, NULL, 'urdu', '2026-05-11 10:22:46', NULL, NULL, 0.00, 0),
(30, 'Battles of Prophet ﷺ — Part 3', 'غزوات کا بیان (حصہ سوم)', NULL, NULL, 'urdu', '2026-05-11 10:26:28', NULL, NULL, 100.00, 0),
(31, 'Major & Minor Sins', 'کبیرہ و صغیرہ گناہوں کا بیان', NULL, NULL, 'urdu', '2026-05-11 10:34:47', '', '', 100.00, 0),
(32, 'Issues of the Beard (Sunnah Grooming)', 'داڑھی کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:36:10', NULL, NULL, 0.00, 0),
(33, 'Issues of Following the Sunnah', 'اتباع سنت کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:37:41', NULL, NULL, 100.00, 0),
(34, 'Description of Intercession', 'شفاعت کا بیان', NULL, NULL, 'urdu', '2026-05-11 10:38:27', '', '', 250.00, 0),
(35, 'Interest & Worldly Matters', 'سودی اور دنیوی', NULL, NULL, 'urdu', '2026-05-11 10:43:15', NULL, NULL, 200.00, 0),
(36, 'Teachings of the Holy Quran', 'تعلیماتِ قرآن مجید', NULL, NULL, 'urdu', '2026-05-11 10:44:17', NULL, NULL, 100.00, 0),
(37, 'Rights of the Prophet ﷺ', 'حقوقِ رسولِ ﷺ', NULL, NULL, 'urdu', '2026-05-11 10:45:30', NULL, NULL, 250.00, 0),
(38, 'Major & Minor Sins', 'کبیرہ و صغیرہ گناہوں کا بیان', NULL, NULL, 'urdu', '2026-05-11 10:47:30', NULL, NULL, 150.00, 0),
(39, 'Virtues of Companions — Part 4', 'فضائلِ صحابہ کرام (حصہ چہارم)', NULL, NULL, 'urdu', '2026-05-11 10:48:47', '', '', 150.00, 0),
(40, 'Issues of Food & Drink', 'کھانے پینے کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:50:52', NULL, NULL, 150.00, 0),
(41, 'Issues of Adornment & Beautification', 'تزئین کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:51:42', NULL, NULL, 150.00, 0),
(42, 'Description of Knowledge (\'Ilm)', 'علم کا بیان', NULL, NULL, 'urdu', '2026-05-11 10:54:34', NULL, NULL, 150.00, 0),
(43, 'Issues of Repentance', 'توبہ کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:56:25', NULL, NULL, 150.00, 0),
(44, 'Issues of Funerals (Janazah)', 'جنائز کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:57:18', NULL, NULL, 150.00, 0),
(45, 'Issues of Fasting (Sawm)', 'روزوں کے مسائل', NULL, NULL, 'urdu', '2026-05-11 10:59:42', NULL, NULL, 150.00, 0),
(46, 'Issues of Jihad', 'جہاد کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:01:07', NULL, NULL, 150.00, 0),
(47, 'Virtues of the Holy Quran', 'فضائلِ قرآن مجید', NULL, NULL, 'urdu', '2026-05-11 11:03:11', NULL, NULL, 150.00, 0),
(48, 'Virtues of Praising Allah', 'فضائلِ حمدِ ربّ العالمین', NULL, NULL, 'urdu', '2026-05-11 11:03:35', NULL, NULL, 0.00, 0),
(49, 'Intimate Supplications', 'مناجات کا بیان', NULL, NULL, 'urdu', '2026-05-11 11:04:10', NULL, NULL, 100.00, 0),
(50, 'Enjoining Good & Forbidding Evil', 'امر بالمعروف و نہی عن المنکر', NULL, NULL, 'urdu', '2026-05-11 11:05:50', NULL, NULL, 150.00, 0),
(51, 'Issues of Divine Decree (Qadar)', 'تقدیر کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:07:38', NULL, NULL, 150.00, 0),
(52, 'Issues of Trade & Commerce', 'خرید و فروخت کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:08:04', NULL, NULL, 150.00, 0),
(53, 'Issues of Faith (Iman)', 'ایمان کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:17:42', 'uploads/covers/1778499393_a196481f6e21d849.webp', '', 0.00, 0),
(54, 'Issues of Ethics & Morality (Akhlaq)', 'اخلاقیات کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:18:09', NULL, NULL, 150.00, 0),
(55, 'Battles of Prophet ﷺ — Part 2', 'غزوات کا بیان (حصہ دوم)', NULL, NULL, 'urdu', '2026-05-11 11:24:56', NULL, NULL, 100.00, 0),
(56, 'Signs of the Day of Judgment', 'علاماتِ قیامت کا بیان', NULL, NULL, 'urdu', '2026-05-11 11:26:24', 'uploads/covers/1778498784_43e8945da61d3adc.webp', NULL, 200.00, 0),
(57, 'Issues of Supplication (Du\'a)', 'دعا کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:27:14', 'uploads/covers/1778498834_bc46b2c29127cc0f.webp', NULL, 120.00, 0),
(58, 'Issues of Devotional Recitations', 'وردِ شریف کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:28:31', 'uploads/covers/1778498911_388afcd97f9f0b2a.webp', NULL, 150.00, 0),
(59, 'Description of Knowledge (\'Ilm)', 'علم کا بیان', NULL, NULL, 'urdu', '2026-05-11 11:29:48', 'uploads/covers/1778498988_bc2813fb7df2d159.webp', NULL, 200.00, 0),
(60, 'Issues of Dreams (Ru\'ya)', 'خوابوں کے مسائل', NULL, NULL, 'urdu', '2026-05-11 11:33:13', 'uploads/covers/1778499215_54b36a4dcca51cb0.webp', '', 100.00, 0),
(61, 'Description of Dress & Clothing', 'لباس کا بیان', NULL, NULL, 'urdu', '2026-05-11 11:34:31', 'uploads/covers/1778499271_2b317ce55652bf05.webp', NULL, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `concepts`
--

CREATE TABLE `concepts` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `book_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concepts`
--

INSERT INTO `concepts` (`id`, `topic_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `book_id`) VALUES
(2, 2, 'wajib', NULL, 1, '2026-05-19 11:31:42', '2026-05-19 11:31:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'test', 'test@gmail.com', 'hello', 'hello', '2026-05-12 06:35:10'),
(2, 'test', 'admin@moneyminds.local', 'hello', 'hello', '2026-05-12 06:39:33'),
(3, 'test', 'admin@moneyminds.local', 'hello', 'hello', '2026-05-12 06:40:20'),
(4, 'test', 'furqan123@gmail.com', 'hello', 'hello', '2026-05-12 06:40:35'),
(5, 'test', 'timeglobaltech@gmail.com', 'hello', 'hello2', '2026-05-12 06:44:30'),
(6, 'hello world', 'furqan@gmail.com', 'hello', 'hello', '2026-05-12 06:47:02'),
(7, 'hello world', 'furqan@gmail.com', 'hello', 'hello', '2026-05-12 06:54:00'),
(8, 'hello world', 'furqan@gmail.com', 'hello', 'hello', '2026-05-12 06:54:48'),
(9, 'test', 'admin@moneyminds.local', 'hello', '         ', '2026-05-16 10:21:36'),
(10, 'test', 'admin@moneyminds.local', 'hello', 'gee', '2026-05-16 10:26:38'),
(11, 'test', 'admin@moneyminds.local', 'hello', 'gee', '2026-05-16 10:27:37'),
(12, 'ahmed', 'ahmed@gmail.com', 'fiqah', 'hello', '2026-06-08 06:34:17'),
(13, 'ahmed', 'ahmed@gmail.com', 'fiqah', 'hello', '2026-06-08 06:43:29'),
(14, 'ahmed', 'ahmed@gmail.com', 'fiqah', 'hello', '2026-06-08 06:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ur` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_lessons` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `format` varchar(50) DEFAULT 'Video',
  `status` varchar(20) DEFAULT 'Ongoing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title_en`, `title_ur`, `description`, `total_lessons`, `category`, `format`, `status`, `created_at`) VALUES
(1, 'Tafsir al-Qur\'an Series', 'تفسیر القرآن', 'Comprehensive study of the Holy Quran.', 48, 'Tafsir', 'Video', 'Ongoing', '2026-04-29 07:42:41'),
(2, 'Forty Hadith Explained', 'اربعین نووی کی شرح', 'Detailed explanation of Imam Nawawi\'s forty hadith.', 40, 'Hadith', 'Video+Audio', 'Completed', '2026-04-29 07:42:41'),
(3, 'Hanafi Fiqh Essentials', 'فقہ حنفی کے بنیادی اصول', 'Core concepts of Hanafi Fiqh.', 32, 'Fiqh', 'Video', 'New', '2026-04-29 07:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `student_email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lesson` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `course_id`, `student_name`, `student_email`, `phone`, `language`, `created_at`, `lesson`) VALUES
(1, 3, 'Test User', 'test@test.com', NULL, 'en', '2026-06-08 06:43:37', 'Lesson 17');

-- --------------------------------------------------------

--
-- Table structure for table `fatwas`
--

CREATE TABLE `fatwas` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `language` varchar(10) DEFAULT 'en',
  `question_text` text NOT NULL,
  `answer_text` text DEFAULT NULL,
  `status` enum('Pending','In Review','Answered','Published') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `answered_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fatwas`
--

INSERT INTO `fatwas` (`id`, `reference_no`, `user_name`, `user_email`, `category`, `language`, `question_text`, `answer_text`, `status`, `created_at`, `answered_at`) VALUES
(1, 'Q-1001', 'Abdullah', 'test@example.com', 'Salah & Worship', 'en', 'What is the correct method of performing Witr according to the Hanafi school?', 'The Hanafi school prescribes 3 rakats of Witr with a single salam at the end, and reading the Qunut in the third rakat before bowing.', 'Published', '2023-01-01 18:00:00', NULL),
(2, 'Q-1002', 'test', 'furqankazimworking@gmail.com', 'Fasting &amp; Zakat', 'en', 'fgh', NULL, 'Pending', '2026-05-08 07:17:55', NULL),
(3, 'Q-1003', 'HELLO', 'admin123@gmail.com', 'Fasting &amp; Zakat', 'en', 'hello world', NULL, 'Pending', '2026-05-16 05:24:16', NULL),
(4, 'Q-1004', 'HELLO', 'admin123@gmail.com', 'Fasting &amp; Zakat', 'en', 'hello', NULL, 'Pending', '2026-05-16 07:22:01', NULL),
(5, 'Q-1005', 'HELLO', 'admin123@gmail.com', 'Fasting &amp; Zakat', 'en', 'zx', NULL, 'Pending', '2026-05-16 07:32:19', NULL),
(6, 'Q-1006', 'HELLO', 'admin123@gmail.com', 'Fasting &amp; Zakat', 'en', 'mnn', NULL, 'Pending', '2026-05-16 08:04:29', NULL),
(7, 'Q-1007', 'ali', 'ali@gmail.com', 'Fasting &amp; Zakat', 'en', 'hello', NULL, 'Pending', '2026-06-08 06:27:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `islamic_scholars`
--

CREATE TABLE `islamic_scholars` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `islamic_scholars`
--

INSERT INTO `islamic_scholars` (`id`, `name`, `specialization`, `bio`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'iqbal kilani', 'fiqh', 'he', 1, '2026-05-19 11:13:28', '2026-05-19 11:13:28'),
(3, 'iqbal kilani', 'fiqh', 'he', 1, '2026-05-19 11:14:18', '2026-05-19 11:14:18'),
(4, 'iqbal kilani', 'fiqh', 'hg', 1, '2026-05-21 06:37:51', '2026-05-21 06:37:51'),
(5, 'iqbal kilani', 'fiqh', 'hg', 1, '2026-05-21 06:38:53', '2026-05-21 06:38:53'),
(6, 'iqbal kilani', 'fiqh', 'hg', 1, '2026-05-21 06:38:56', '2026-05-21 06:38:56'),
(7, 'iqbal kilani', 'fiqh', NULL, 1, '2026-05-21 06:41:37', '2026-05-21 06:41:37'),
(8, 'iqbal kilani', 'fiqh', NULL, 1, '2026-05-21 06:41:49', '2026-05-21 06:41:49'),
(9, 'iqbal kilani', 'fiqh', 'hg', 1, '2026-05-21 06:42:24', '2026-05-21 06:42:24');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `book_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `status`, `created_at`) VALUES
(1, 1, 'Test User', 'test@test.com', '12345', '123 Test St', 'Completed', '2026-04-29 07:58:46'),
(2, 1, 'Test User', 'test@test.com', '12345', '123 Test St', 'Completed', '2026-04-29 08:00:57'),
(3, 4, 'Furqan Kazim', 'furqankazimworking@gmail.com', '032145678', '498498549', 'Pending', '2026-04-29 07:13:02'),
(4, 6, 'Furqan Kazim', 'furqankazimworking@gmail.com', '03311000', '1000', 'Pending', '2026-05-01 06:34:02'),
(5, 6, 'Furqan Kazim', 'furqankazimworking@gmail.com', '03311000', '03', 'Completed', '2026-05-01 07:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) NOT NULL,
  `changed_by` varchar(50) DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`id`, `order_id`, `old_status`, `new_status`, `changed_by`, `changed_at`) VALUES
(1, 5, 'Pending', 'Shipped', 'admin', '2026-05-01 11:56:27'),
(2, 5, 'Shipped', 'Completed', 'admin', '2026-05-04 10:31:41'),
(3, 1, 'Shipped', 'Completed', 'admin', '2026-05-04 10:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `islamic_scholar_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `book_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `islamic_scholar_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `book_id`) VALUES
(2, 2, 'fiqh', NULL, 1, '2026-05-19 11:31:32', '2026-05-19 11:31:32', NULL),
(3, 2, 'fiqh', NULL, 1, '2026-05-20 05:58:10', '2026-05-20 05:58:10', 23),
(4, 3, 'aqeedah', NULL, 1, '2026-05-20 07:58:51', '2026-05-20 07:58:51', 44);

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `book_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `subject_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `book_id`) VALUES
(2, 2, 'salah', NULL, 1, '2026-05-19 11:31:37', '2026-05-19 11:31:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `status`, `created_at`) VALUES
(1, 'Test User', 'test@test.com', '$2y$10$obbPvh.SGagxVPPjLPvN6e9a8Gy4DW6Msz70/bzmabYP12aH/EEri', 'active', '2026-05-01 12:49:04'),
(2, 'Irfan Nawaz', 'irfan@test.com', '$2y$10$j4G5wsdvpxpzChzYO8k/0eTnGLUhl8FYXffjXEQRu6xOYX5767GVu', 'active', '2026-05-01 13:14:03'),
(3, 'irfan', 'irfan1@test.com', '$2y$10$HqPFb6qgbQTwWHC1h0i.1e/2uyrU.E9ObQ/aBeLX3izSbQrmyokCO', 'active', '2026-05-01 13:43:18'),
(4, 'Furqan Kazim', 'furqankazimworking@gmail.com', '$2y$10$mK8qeTCKmAREABdFobI9N.q5VhkP5zMX05cqRNnfP1BTRjdUxxXpu', 'active', '2026-05-02 10:49:35'),
(5, 'Networking_system', 'thefnetwork9@gmail.com', '$2y$10$eh7oOoXSLqMgbCiryIPvEu9D68Am/jpVg6LOpNeVpDptbaPaTE.PS', 'active', '2026-05-04 09:30:16'),
(6, 'testuser', 'testuser@gmail.com', '$2y$10$dosK853w89E9Fl80Xzvmhuws7qcpHRJkf.8xjzCQ4/ut7jqfo9Zoq', 'active', '2026-06-08 04:26:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `audios`
--
ALTER TABLE `audios`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concepts`
--
ALTER TABLE `concepts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `fk_concept_book` (`book_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `fatwas`
--
ALTER TABLE `fatwas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`);

--
-- Indexes for table `islamic_scholars`
--
ALTER TABLE `islamic_scholars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `islamic_scholar_id` (`islamic_scholar_id`),
  ADD KEY `fk_subject_book` (`book_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `fk_topic_book` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audios`
--
ALTER TABLE `audios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `concepts`
--
ALTER TABLE `concepts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fatwas`
--
ALTER TABLE `fatwas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `islamic_scholars`
--
ALTER TABLE `islamic_scholars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `concepts`
--
ALTER TABLE `concepts`
  ADD CONSTRAINT `concepts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_concept_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_subject_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`islamic_scholar_id`) REFERENCES `islamic_scholars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `fk_topic_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
