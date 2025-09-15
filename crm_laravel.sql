-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 15, 2025 at 12:29 PM
-- Server version: 8.0.43
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm_laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_assistants`
--

CREATE TABLE `academic_assistants` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_assistants`
--

INSERT INTO `academic_assistants` (`id`, `name`, `email`, `phone`, `code`, `address`, `is_active`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'assistant', 'assistant@crm.com', '9685632122', '91', NULL, 1, 1, 1, NULL, '2025-09-14 23:49:43', '2025-09-14 23:49:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `converted_leads`
--

CREATE TABLE `converted_leads` (
  `id` bigint UNSIGNED NOT NULL,
  `lead_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `board_id` bigint UNSIGNED DEFAULT NULL,
  `batch_id` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `academic_assistant_id` bigint UNSIGNED DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `converted_leads`
--

INSERT INTO `converted_leads` (`id`, `lead_id`, `name`, `code`, `phone`, `email`, `board_id`, `batch_id`, `course_id`, `academic_assistant_id`, `subject_id`, `remarks`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 41, 'MIDLAJ', '91', '9645195695', NULL, NULL, NULL, 1, 8, NULL, 'test', 1, 1, NULL, '2025-09-15 00:25:18', '2025-09-15 00:25:18', NULL),
(2, 41, 'MIDLAJ', '91', '9645195695', NULL, NULL, NULL, 1, 8, NULL, 'converted', 2, 2, NULL, '2025-09-15 04:05:46', '2025-09-15 04:05:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `title`, `code`, `phone_code`, `is_active`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'India', 'IN', '91', 1, '2025-09-13 02:55:31', '2025-09-13 02:55:31', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fees` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `duration`, `fees`, `is_active`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'Sample Course', NULL, NULL, NULL, 1, '2025-09-12 00:54:54', '2025-09-13 01:56:46', NULL, NULL),
(2, 'Inactive Course', NULL, NULL, NULL, 0, '2025-09-12 00:54:54', '2025-09-12 00:55:46', NULL, '2025-09-12 00:55:46'),
(3, 'No Checkbox Course', NULL, NULL, NULL, 0, '2025-09-12 00:54:54', '2025-09-12 00:55:49', NULL, '2025-09-12 00:55:49'),
(4, 'Updated Test Course', NULL, NULL, NULL, 0, '2025-09-12 00:57:10', '2025-09-12 00:57:58', NULL, '2025-09-12 00:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `country_id` bigint UNSIGNED DEFAULT NULL,
  `interest_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_status_id` bigint UNSIGNED DEFAULT NULL,
  `lead_source_id` bigint UNSIGNED DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telecaller_id` bigint UNSIGNED DEFAULT NULL,
  `team_id` bigint UNSIGNED DEFAULT NULL,
  `place` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `by_meta` tinyint(1) NOT NULL DEFAULT '0',
  `meta_lead_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_converted` tinyint(1) NOT NULL DEFAULT '0',
  `followup_date` date DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `title`, `gender`, `age`, `phone`, `code`, `whatsapp`, `whatsapp_code`, `email`, `qualification`, `country_id`, `interest_status`, `lead_status_id`, `lead_source_id`, `address`, `telecaller_id`, `team_id`, `place`, `created_by`, `updated_by`, `deleted_by`, `course_id`, `by_meta`, `meta_lead_id`, `is_converted`, `followup_date`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ameer', NULL, NULL, '9946432377', '91', NULL, NULL, NULL, NULL, NULL, NULL, 7, 1, NULL, 2, NULL, NULL, 1, 1, NULL, 1, 0, NULL, 0, NULL, 'test', '2025-09-12 01:19:43', '2025-09-13 03:26:34', NULL),
(2, 'Suhail', NULL, NULL, '9656565622', '91', NULL, NULL, NULL, NULL, NULL, NULL, 3, 1, NULL, 4, NULL, NULL, 1, 1, NULL, 1, 0, NULL, 0, NULL, 'demo', '2025-09-12 03:20:54', '2025-09-13 04:41:05', NULL),
(3, 'Anu anas', NULL, NULL, '7025234214', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Moonniyur', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(4, 'ASHIK KOTTAYIL', NULL, NULL, '9544849199', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Kollam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(5, 'سفية ', NULL, NULL, '9747948313', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kottakkal', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(6, 'sreejaya', NULL, NULL, '7561871149', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'kottarakara', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(7, 'Jyothish', NULL, NULL, '9745650734', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Calicut', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(8, 'Naseera Moosa', NULL, NULL, '9645713535', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Manjeri', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(9, 'Thomas Varghese Thuruthel', NULL, NULL, '9061395082', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Ramamangalam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(10, 'Jisha Arshad', NULL, NULL, '9483470856', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Malappuram', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(11, 'نبيل بن رفيق', NULL, NULL, '7510511412', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kannur', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(12, 'Jayakrishnan.s', NULL, NULL, '8086262597', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Kollam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(13, 'afeef', NULL, NULL, '9544157169', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kozikode', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:46', '2025-09-13 06:22:46', NULL),
(14, 'Vinod e', NULL, NULL, '1521277885', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Kasaragod', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(15, 'Sreekanth Sree', NULL, NULL, '9847731704', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Thiruvananthapuram', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(16, 'Lambrath  KA', NULL, NULL, '9995742653', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'guruvayoor', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(17, 'Gopan Sagara', NULL, NULL, '9946468552', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Thiruvananthapuram', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(18, 'Rishal', NULL, NULL, '8136834556', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Malappuram', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(19, 'naseera', NULL, NULL, '8129172441', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'calicut', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(20, 'nisamudheem', NULL, NULL, '6558117430', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Dammam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(21, 'Anoop Vellampuram', NULL, NULL, '9895902117', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'malappuram', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(22, 'Jamsheer Appu Mk', NULL, NULL, '9562175529', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'wayanad', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(23, 'jayaras', NULL, NULL, '9633264806', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kovalam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(24, 'Suma Vijayakumar', NULL, NULL, '8943863393', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Pathanamthitta', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(25, 'Nikhil k mathew', NULL, NULL, '8848450826', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Adimali', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(26, 'Vimal', NULL, NULL, '9633266171', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Thrissur', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(27, 'Mathew Varghese', NULL, NULL, '8881316777', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Chandauli', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(28, 'j_o_s_i_y_a_  (@_@)', NULL, NULL, '8138860607', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Meenadom', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(29, 'aKkuhh', NULL, NULL, '8606129180', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kerala', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(30, 'Rajesh mj', NULL, NULL, '9745501101', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'angamali', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(31, 'PRAKASAN AM', NULL, NULL, '9847507294', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Koyilandy', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(32, 'sadik ak', NULL, NULL, '9745329418', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Edakkara', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(33, 'ر .', NULL, NULL, '7594913992', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'City', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(34, 'Anandhuuuuuu', NULL, NULL, '9745708370', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Kollam', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(35, 'Ayaan Dev', NULL, NULL, '8921587112', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kasaragod', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(36, 'Ziyan', NULL, NULL, '9656423190', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Kannur', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(37, 'Abhinav', NULL, NULL, '6238504656', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Calicut', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(38, 'NIHAL NOUSHAD', NULL, NULL, '9747028771', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 4, NULL, 'Pattambi', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(39, 'abhinandh_sidharth', NULL, NULL, '8943851889', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, NULL, 'Kozhikode', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-13 06:22:47', NULL),
(40, 'ADHIL.☆', NULL, NULL, '9037286955', '91', NULL, NULL, NULL, NULL, NULL, NULL, 4, 2, NULL, 4, NULL, 'Parapangdi', 1, 1, NULL, 1, 0, NULL, 0, NULL, NULL, '2025-09-13 06:22:47', '2025-09-15 03:45:44', NULL),
(41, 'MIDLAJ', NULL, NULL, '9645195695', '91', NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, NULL, 2, NULL, 'Vazhakkad', 1, 2, NULL, 1, 0, NULL, 1, NULL, NULL, '2025-09-13 06:22:47', '2025-09-15 04:05:46', NULL),
(42, 'Lead test', NULL, NULL, '9656451233', '91', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0, NULL, 0, NULL, NULL, '2025-09-15 01:26:08', '2025-09-15 03:22:54', NULL),
(43, 'Lead test', NULL, NULL, '9656451233', '91', NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, NULL, 0, NULL, NULL, '2025-09-15 01:33:24', '2025-09-15 03:39:34', NULL),
(44, 'Test', NULL, NULL, '9696969696', '91', NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0, NULL, 0, NULL, NULL, '2025-09-15 01:42:18', '2025-09-15 03:37:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_activities`
--

CREATE TABLE `lead_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `lead_id` bigint UNSIGNED NOT NULL,
  `lead_status_id` bigint UNSIGNED DEFAULT NULL,
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `followup_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_activities`
--

INSERT INTO `lead_activities` (`id`, `created_at`, `updated_at`, `lead_id`, `lead_status_id`, `activity_type`, `description`, `followup_date`, `remarks`, `created_by`, `updated_by`, `deleted_by`, `deleted_at`) VALUES
(1, '2025-09-12 01:23:01', '2025-09-12 01:23:01', 1, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(2, '2025-09-12 01:30:17', '2025-09-12 01:30:17', 1, NULL, 'test_soft_delete', 'Testing soft delete functionality', NULL, NULL, 1, NULL, NULL, '2025-09-12 01:30:17'),
(3, '2025-09-12 03:20:54', '2025-09-12 03:20:54', 2, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(4, '2025-09-12 03:33:44', '2025-09-12 03:33:44', 2, 2, 'status_update', 'test', '2025-09-12', 'test', 1, 1, NULL, NULL),
(5, '2025-09-13 01:14:56', '2025-09-13 01:14:56', 2, 3, 'status_update', 'Status updated to Interested', '2025-09-13', 'test', 1, 1, NULL, NULL),
(6, '2025-09-13 03:26:22', '2025-09-13 03:26:22', 1, 2, 'status_update', 'Status updated to Follow-up', '2025-09-13', NULL, 1, 1, NULL, NULL),
(7, '2025-09-13 03:26:34', '2025-09-13 03:26:34', 1, 7, 'status_update', 'Status updated to Interested to Buy', '2025-09-13', NULL, 1, 1, NULL, NULL),
(8, '2025-09-13 03:28:15', '2025-09-13 03:28:15', 2, 7, 'status_update', 'Status updated to Interested to Buy', '2025-09-13', NULL, 1, 1, NULL, NULL),
(122, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 3, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(123, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 4, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(124, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 5, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(125, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 6, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(126, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 7, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(127, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 8, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(128, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 9, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(129, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 10, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(130, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 11, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(131, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 12, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(132, '2025-09-13 06:22:46', '2025-09-13 06:22:46', 13, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(133, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 14, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(134, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 15, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(135, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 16, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(136, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 17, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(137, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 18, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(138, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 19, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(139, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 20, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(140, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 21, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(141, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 22, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(142, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 23, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(143, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 24, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(144, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 25, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(145, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 26, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(146, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 27, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(147, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 28, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(148, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 29, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(149, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 30, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(150, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 31, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(151, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 32, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(152, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 33, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(153, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 34, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(154, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 35, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(155, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 36, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(156, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 37, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(157, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 38, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(158, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 39, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(159, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 40, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(160, '2025-09-13 06:22:47', '2025-09-13 06:22:47', 41, NULL, 'bulk_upload', 'Lead created via bulk upload', NULL, NULL, 1, NULL, NULL, NULL),
(161, '2025-09-15 01:44:11', '2025-09-15 01:44:11', 44, 2, 'status_update', 'Status updated to Follow-up', '2025-09-15', 'ttest', 1, 1, NULL, NULL),
(162, '2025-09-15 02:14:14', '2025-09-15 02:14:14', 40, 4, 'status_update', 'Status updated to Disqualified', '2025-09-15', 'ttest', 1, 1, NULL, NULL),
(163, '2025-09-15 02:14:53', '2025-09-15 02:14:53', 44, 2, 'status_update', 'Status updated to Follow-up', '2025-09-15', 'ttest', 1, 1, NULL, NULL),
(164, '2025-09-15 02:15:10', '2025-09-15 02:15:10', 44, 3, 'status_update', 'Status updated to Not-interested IN FULL COURSE', '2025-09-15', 'ttest', 1, 1, NULL, NULL),
(165, '2025-09-15 03:37:31', '2025-09-15 03:37:31', 44, 4, 'status_update', 'Status updated to Disqualified', '2025-09-15', 'Status changed from \'Not-interested IN FULL COURSE\' to \'Disqualified\' | User Note: testing', 1, 1, NULL, NULL),
(166, '2025-09-15 03:39:34', '2025-09-15 03:39:34', 43, 4, 'status_update', 'Status updated to Disqualified', '2025-09-15', 'Status changed from \'Un Touched Leads\' to \'Disqualified\' | User Note: testing', 1, 1, NULL, NULL),
(167, '2025-09-15 03:40:53', '2025-09-15 03:40:53', 43, 4, 'status_update', 'Status updated to Disqualified', '2025-09-15', 'Status changed from \'Disqualified\' to \'Disqualified\' | User Note: testing', 1, 1, NULL, NULL),
(168, '2025-09-15 03:45:29', '2025-09-15 03:45:29', 40, 2, 'status_update', 'Status updated to Follow-up', '2025-09-15', 'Status changed from \'Un Touched Leads\' to \'Follow-up\' | User Note: test', 1, 1, NULL, NULL),
(169, '2025-09-15 03:45:44', '2025-09-15 03:45:44', 40, 4, 'status_update', 'Status updated to Disqualified', '2025-09-15', 'Status changed from \'Follow-up\' to \'Disqualified\' | User Note: hbhbjn   n', 1, 1, NULL, NULL),
(170, '2025-09-15 04:05:11', '2025-09-15 04:05:11', 41, 2, 'status_update', 'Status updated to Follow-up', '2025-09-15', 'Status changed from \'Un Touched Leads\' to \'Follow-up\' | User Note: will update', 2, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `title`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'Google Ad', 'Leads from Google Ad', 1, '2025-09-10 05:48:24', '2025-09-13 02:17:13', NULL, NULL),
(2, 'Facebook Instagram Ad', 'Leads from Meta Ad', 1, '2025-09-10 05:48:24', '2025-09-13 02:47:22', NULL, NULL),
(3, 'Seminar', 'Leads from Seminar', 1, '2025-09-10 05:48:24', '2025-09-13 02:47:39', NULL, NULL),
(4, 'Reference', 'Leads from Reference', 1, '2025-09-10 05:48:24', '2025-09-13 02:47:49', NULL, NULL),
(5, 'Others', 'Other Sources', 1, '2025-09-10 05:48:24', '2025-09-13 02:48:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_statuses`
--

CREATE TABLE `lead_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_statuses`
--

INSERT INTO `lead_statuses` (`id`, `title`, `description`, `color`, `is_active`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'Un Touched Leads', 'Un Touched Leads', '#28a745', 1, '2025-09-10 05:48:24', '2025-09-13 01:48:01', NULL, NULL),
(2, 'Follow-up', 'Lead Follow-up', '#17a2b8', 1, '2025-09-10 05:48:24', '2025-09-13 01:48:26', NULL, NULL),
(3, 'Not-interested IN FULL COURSE', 'Not-interested IN FULL COURSE', '#ffc107', 1, '2025-09-10 05:48:24', '2025-09-13 01:48:56', NULL, NULL),
(4, 'Disqualified', 'Lead is Disqualified', '#dc3545', 1, '2025-09-10 05:48:24', '2025-09-13 01:49:27', NULL, NULL),
(5, 'DNP', 'DNP', '#6f42c1', 1, '2025-09-10 05:48:24', '2025-09-13 01:49:41', NULL, NULL),
(6, 'Demo', 'Demo', '#20c997', 1, '2025-09-10 05:48:24', '2025-09-13 01:50:06', NULL, NULL),
(7, 'Interested to Buy', 'Interested to Buy', '#6c757d', 1, '2025-09-10 05:48:24', '2025-09-13 01:51:02', NULL, NULL),
(8, 'Positive', 'Positive', NULL, 1, '2025-09-13 01:51:29', '2025-09-13 01:51:29', NULL, NULL),
(9, 'May Buy Later', 'May Buy Later', NULL, 1, '2025-09-13 01:52:03', '2025-09-13 01:52:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_10_084415_create_user_roles_table', 1),
(5, '2025_09_10_084435_create_teams_table', 1),
(6, '2025_09_10_084444_create_lead_statuses_table', 1),
(7, '2025_09_10_084452_create_lead_sources_table', 1),
(8, '2025_09_10_084511_create_countries_table', 1),
(9, '2025_09_10_084520_create_courses_table', 1),
(10, '2025_09_10_100551_create_leads_table', 1),
(11, '2025_09_10_102239_create_lead_activities_table', 2),
(12, '2025_09_10_113154_add_missing_columns_to_leads_table', 2),
(13, '2025_09_11_053026_add_team_id_to_leads_table', 3),
(14, '2025_09_11_054906_create_settings_table', 4),
(15, '2025_09_12_054837_add_is_active_to_teams_table', 5),
(16, '2025_09_12_055709_add_is_active_to_users_table', 6),
(17, '2025_09_12_065113_add_columns_to_lead_activities_table', 7),
(18, '2025_09_12_065848_add_deleted_at_to_lead_activities_table', 8),
(20, '2025_01_15_000000_create_converted_leads_table', 1),
(21, '2025_09_11_051129_create_lead_statuses_table', 2),
(22, '2025_09_11_051144_create_lead_sources_table', 2),
(23, '2025_09_11_051149_create_countries_table', 2),
(24, '2025_09_11_051153_create_courses_table', 2),
(25, '2025_09_11_051158_create_teams_table', 2),
(26, '2025_09_11_051201_create_telecallers_table', 2),
(27, '2025_09_11_051205_update_user_roles_table', 2),
(28, '2025_09_13_100437_add_deleted_at_to_converted_leads_table', 2),
(29, '2025_09_11_122757_add_whatsapp_code_to_leads_table', 9),
(30, '2025_09_12_100508_add_site_settings_to_settings_table', 9),
(31, '2025_09_13_101756_add_deleted_by_to_all_tables', 10),
(32, '2025_09_13_114439_create_academic_assistants_table', 11),
(33, '2025_09_15_062308_create_voxbay_call_logs_table', 12),
(34, '2025_09_15_063039_add_ext_no_to_users_table', 13),
(35, '2025_09_15_112542_create_notifications_table', 14),
(36, '2025_09_15_112719_create_notification_reads_table', 14),
(37, '2025_09_15_113019_update_notifications_table_make_role_id_required', 15),
(38, '2025_09_15_113443_remove_scheduled_at_from_notifications_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','success','warning','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `target_type` enum('all','role','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `role_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `target_type`, `role_id`, `user_id`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'New Leads Uploaded', 'New Leads Uploaded', 'success', 'all', 1, NULL, 1, 1, '2025-09-15 06:16:02', '2025-09-15 06:16:02'),
(2, 'Test Notification', 'This is a test notification to verify the system is working.', 'info', 'all', 1, NULL, 1, 1, '2025-09-15 06:17:33', '2025-09-15 06:17:33'),
(3, 'Test Notification', 'This is a test notification to verify the system is working.', 'info', 'all', 1, NULL, 1, 1, '2025-09-15 06:18:03', '2025-09-15 06:18:03'),
(4, 'Test for Non-Admin Users', 'This notification should only be visible to non-admin users.', 'info', 'all', 3, NULL, 1, 1, '2025-09-15 06:21:45', '2025-09-15 06:21:45'),
(5, 'Auto Read Test - Page Load', 'This notification should be automatically marked as read when page loads.', 'warning', 'all', 3, NULL, 1, 1, '2025-09-15 06:24:27', '2025-09-15 06:24:27'),
(6, 'Test Notification #1', 'This is test notification number 1 to test the scrolling functionality in the dropdown.', 'info', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(7, 'Test Notification #2', 'This is test notification number 2 to test the scrolling functionality in the dropdown.', 'success', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(8, 'Test Notification #3', 'This is test notification number 3 to test the scrolling functionality in the dropdown.', 'warning', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(9, 'Test Notification #4', 'This is test notification number 4 to test the scrolling functionality in the dropdown.', 'error', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(10, 'Test Notification #5', 'This is test notification number 5 to test the scrolling functionality in the dropdown.', 'info', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(11, 'Test Notification #6', 'This is test notification number 6 to test the scrolling functionality in the dropdown.', 'success', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(12, 'Test Notification #7', 'This is test notification number 7 to test the scrolling functionality in the dropdown.', 'warning', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54'),
(13, 'Test Notification #8', 'This is test notification number 8 to test the scrolling functionality in the dropdown.', 'error', 'all', 3, NULL, 1, 1, '2025-09-15 06:28:54', '2025-09-15 06:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `notification_reads`
--

CREATE TABLE `notification_reads` (
  `id` bigint UNSIGNED NOT NULL,
  `notification_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `read_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_reads`
--

INSERT INTO `notification_reads` (`id`, `notification_id`, `user_id`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2025-09-15 06:16:34', '2025-09-15 06:16:20', '2025-09-15 06:16:34'),
(2, 3, 2, '2025-09-15 06:24:54', '2025-09-15 06:24:50', '2025-09-15 06:24:54'),
(3, 2, 2, '2025-09-15 06:24:54', '2025-09-15 06:24:50', '2025-09-15 06:24:54'),
(4, 5, 2, '2025-09-15 06:24:54', '2025-09-15 06:24:50', '2025-09-15 06:24:54'),
(5, 4, 2, '2025-09-15 06:24:54', '2025-09-15 06:24:50', '2025-09-15 06:24:54'),
(6, 8, 2, '2025-09-15 06:29:30', '2025-09-15 06:29:30', '2025-09-15 06:29:30'),
(7, 9, 2, '2025-09-15 06:29:30', '2025-09-15 06:29:30', '2025-09-15 06:29:30'),
(8, 7, 2, '2025-09-15 06:29:30', '2025-09-15 06:29:30', '2025-09-15 06:29:30'),
(9, 6, 2, '2025-09-15 06:29:30', '2025-09-15 06:29:30', '2025-09-15 06:29:30'),
(15, 13, 2, '2025-09-15 06:30:13', '2025-09-15 06:30:12', '2025-09-15 06:30:13'),
(16, 10, 2, '2025-09-15 06:30:13', '2025-09-15 06:30:12', '2025-09-15 06:30:13'),
(17, 11, 2, '2025-09-15 06:30:13', '2025-09-15 06:30:12', '2025-09-15 06:30:13'),
(18, 12, 2, '2025-09-15 06:30:13', '2025-09-15 06:30:12', '2025-09-15 06:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('jiNiUGvM38Gi32wIMaM72LZuOi9MWSuwB7wCSKOl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo2OiJfdG9rZW4iO3M6NDA6IndPUU01WWJKc2FtNkNEOXNQZzVaV1MxaFhFUnJBQVI1R3BldHpPN2oiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwczovL2NybS1kZW1vLnRlc3QvYWRtaW4vbm90aWZpY2F0aW9ucyI7fXM6NzoidXNlcl9pZCI7aToxO3M6MTI6ImlzX3RlYW1fbGVhZCI7YjowO3M6MTU6ImlzX3RlYW1fbWFuYWdlciI7YjowO3M6Nzoicm9sZV9pZCI7aToxO3M6MTA6InJvbGVfdGl0bGUiO3M6MTE6IlN1cGVyIEFkbWluIjtzOjk6InVzZXJfbmFtZSI7czoxMDoiQWRtaW4gVXNlciI7czoxMDoidXNlcl9lbWFpbCI7czoxMzoiYWRtaW5AY3JtLmNvbSI7czoxMjoiaXNfbG9nZ2VkX2luIjtiOjE7czoxMjoibG9nZ2VkX2luX2F0IjtpOjE3NTc5MzQxMDg7fQ==', 1757937915),
('tQZJJ2TXYCuLFyy4bxwY7QvbEcaS208W2Cvo1jzu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo2OiJfdG9rZW4iO3M6NDA6Im1YVXE3Y01FQ0FScE94WjBmQW5Wc2NFZHRJbTUzMWkxcHNCdTBJT3QiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwczovL2NybS1kZW1vLnRlc3QvYXBpL25vdGlmaWNhdGlvbnMiO31zOjc6InVzZXJfaWQiO2k6MjtzOjEyOiJpc190ZWFtX2xlYWQiO2I6MTtzOjE1OiJpc190ZWFtX21hbmFnZXIiO2I6MDtzOjc6InJvbGVfaWQiO2k6MztzOjEwOiJyb2xlX3RpdGxlIjtzOjEwOiJUZWxlY2FsbGVyIjtzOjk6InVzZXJfbmFtZSI7czoxMjoiQU1FRVIgU1VIQUlMIjtzOjEwOiJ1c2VyX2VtYWlsIjtzOjE1OiJhbWVlckBnbWFpbC5jb20iO3M6MTI6ImlzX2xvZ2dlZF9pbiI7YjoxO3M6MTI6ImxvZ2dlZF9pbl9hdCI7aToxNzU3OTM1MTQ1O30=', 1757939343);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `group`, `is_public`, `created_at`, `updated_at`, `deleted_by`) VALUES
(4, 'contact_phone', '+1-234-567-8900', 'text', 'Contact phone number', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(5, 'contact_email', 'info@basecrm.com', 'text', 'Contact email address', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(6, 'contact_address', '123 Business Street, City, State 12345', 'text', 'Contact address', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(7, 'email_from_name', 'Base CRM', 'text', 'Email sender name', 'email', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(8, 'email_from_address', 'noreply@basecrm.com', 'text', 'Email sender address', 'email', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(9, 'facebook_url', '', 'text', 'Facebook page URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(10, 'twitter_url', '', 'text', 'Twitter profile URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(11, 'linkedin_url', '', 'text', 'LinkedIn profile URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(12, 'timezone', 'UTC', 'text', 'System timezone', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(13, 'date_format', 'Y-m-d', 'text', 'Date format', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(14, 'time_format', 'H:i:s', 'text', 'Time format', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51', NULL),
(20, 'sidebar_color', '#db0000', 'color', 'Sidebar background color', 'theme', 0, '2025-09-13 04:50:18', '2025-09-13 06:52:00', NULL),
(22, 'topbar_color', '#ffffff', 'color', 'Topbar background color', 'theme', 0, '2025-09-13 04:50:18', '2025-09-13 06:36:43', NULL),
(25, 'site_name', 'Base CRM', 'text', 'Website name displayed in title and header', 'site', 1, '2025-09-13 04:50:18', '2025-09-13 04:50:18', NULL),
(26, 'site_description', 'CRM Management System', 'text', 'Website description for SEO and meta tags', 'site', 1, '2025-09-13 04:50:18', '2025-09-13 04:50:18', NULL),
(27, 'site_logo', 'storage/logo.png', 'file', 'Website logo file path', 'site', 1, '2025-09-13 04:50:18', '2025-09-13 04:50:18', NULL),
(28, 'site_favicon', 'storage/favicon.ico', 'file', 'Website favicon file path', 'site', 1, '2025-09-13 04:50:18', '2025-09-13 04:50:18', NULL),
(29, 'bg_image', 'storage/auth-bg.jpg', 'file', 'Login page background image', 'site', 1, '2025-09-13 04:50:18', '2025-09-13 06:51:53', NULL),
(30, 'login_primary_color', '#667eea', 'color', 'Primary color for login form', 'theme', 0, '2025-09-13 04:50:18', '2025-09-13 06:36:43', NULL),
(31, 'login_secondary_color', '#764ba2', 'color', 'Secondary color for login form', 'theme', 0, '2025-09-13 04:50:18', '2025-09-13 06:36:43', NULL),
(32, 'login_form_style', 'modern', 'text', 'Login form style', 'theme', 0, '2025-09-13 04:50:18', '2025-09-13 06:36:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `team_lead_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `team_lead_id`, `created_by`, `updated_by`, `deleted_by`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Team 1', 'team 1', 2, 1, NULL, NULL, 1, '2025-09-11 01:07:33', '2025-09-15 01:08:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ext_no` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Extension number for Voxbay calling',
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `is_team_lead` tinyint(1) NOT NULL DEFAULT '0',
  `is_team_manager` tinyint(1) NOT NULL DEFAULT '0',
  `current_role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `ext_no`, `code`, `otp`, `profile_picture`, `role_id`, `is_team_lead`, `is_team_manager`, `current_role`, `team_id`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@crm.com', NULL, '$2y$12$s1J6SzGP692lMklJZBwe2O7y7H8V12M6v3z.P3jFruJqBRZWYPZdS', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, 1, NULL, '2025-09-10 05:48:27', '2025-09-10 05:48:27', NULL, NULL),
(2, 'AMEER SUHAIL', 'ameer@gmail.com', NULL, '$2y$12$Zs/VVQNVb/ZB8ACGnNLWg.uZ6RJpx4DIfjOQLr2SNJtty.9Et3puu', '9946432377', '222', '91', NULL, NULL, 3, 1, 0, NULL, 1, 1, NULL, '2025-09-11 23:15:39', '2025-09-15 01:08:29', NULL, NULL),
(3, 'Fidha', 'fidh@admin.com', NULL, '$2y$12$gHM9IViETrvgL7nhcDK0fO/B0g6rK0Y1Xq6uFilfzl6r/pvFss.Ui', '9966552244', NULL, '91', NULL, NULL, 2, 0, 0, NULL, NULL, 1, NULL, '2025-09-12 04:21:32', '2025-09-12 04:21:32', NULL, NULL),
(4, 'Telecaller 2', 'tellecaller2@gmail.com', NULL, '$2y$12$zQ55Kv2fnqLXJ6EM4gEH2uEtnyk6rt0pZee4aTdEiQhWiMW0E66Yi', '9656452311', NULL, '91', NULL, NULL, 3, 0, 0, NULL, 1, 1, NULL, '2025-09-12 22:55:08', '2025-09-12 23:03:49', NULL, NULL),
(5, 'sss', 'sss@gmail.com', NULL, '$2y$12$/JFNSfNMVnu9TsKV08Y63u7s5KOV8FDmtdOmMmxfO85Z5/Dl7mxKa', '9653231214', NULL, '91', NULL, NULL, 4, 0, 0, NULL, NULL, 1, NULL, '2025-09-14 23:30:35', '2025-09-15 00:18:54', NULL, '2025-09-15 00:18:54'),
(6, 'rr', 'ed@gmail.com', NULL, '$2y$12$f5oiTPTkK01G7RdLHkM5Ue9TzZFj7FfCBITSbPpoLM.qzKOASFd/q', '9656854212', NULL, '91', NULL, NULL, 4, 0, 0, NULL, NULL, 1, NULL, '2025-09-14 23:48:46', '2025-09-15 00:18:45', NULL, NULL),
(7, 'Admission Counsellor', 'counsellor@crm.com', NULL, '$2y$12$3r48BLrN4n9joak7dv.cheWgHpCK68U2W4BdH7mBPV8TSc7ksVl0i', '9946523211', NULL, '91', NULL, NULL, 4, 0, 0, NULL, NULL, 1, NULL, '2025-09-15 00:21:13', '2025-09-15 00:21:13', NULL, NULL),
(8, 'Academic Assistant', 'academicassistant@crm.com', NULL, '$2y$12$oYjhpdfUL6A58GFf.CiPM.oVQMeJlJcDkIL3T6PfsdOomG3udqfjG', '9946432233', NULL, '91', NULL, NULL, 5, 0, 0, NULL, NULL, 1, NULL, '2025-09-15 00:24:58', '2025-09-15 00:24:58', NULL, NULL),
(9, 'Test', 'test@crm.com', NULL, '$2y$12$SGRq/htmrfEDyMNKmaA66OBybcgoiU5B/fDX4Pw0o6xbt8H9T18zW', '9638527410', NULL, '91', NULL, NULL, 3, 0, 0, NULL, 1, 1, NULL, '2025-09-15 03:13:13', '2025-09-15 03:13:19', NULL, '2025-09-15 03:13:19'),
(10, 'John', 'john@crm.com', NULL, '$2y$12$4c6A3YNmXvSjdB3rxGWVoeaEauiKrYwzZHFPbjfeDAxd493HQUJyC', '9685748596', NULL, '91', NULL, NULL, 3, 0, 0, NULL, NULL, 1, NULL, '2025-09-15 05:21:21', '2025-09-15 05:21:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `title`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'Super Admin', 'Full system access with all permissions', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL, NULL),
(2, 'Admin', 'Administrative access with user management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL, NULL),
(3, 'Telecaller', 'Telecaller access for lead management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL, NULL),
(4, 'Admission Counsellor', 'Admission Counsellor access for Converted lead management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL, NULL),
(5, 'Academic Assistant', 'Academic Assistant access for Converted lead management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `voxbay_call_logs`
--

CREATE TABLE `voxbay_call_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('incoming','outgoing','missedcall') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_uuid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calledNumber` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callerNumber` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AgentNumber` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extensionNumber` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destinationNumber` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `callerid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `recording_URL` varchar(260) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_assistants`
--
ALTER TABLE `academic_assistants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academic_assistants_email_unique` (`email`),
  ADD KEY `academic_assistants_created_by_foreign` (`created_by`),
  ADD KEY `academic_assistants_updated_by_foreign` (`updated_by`),
  ADD KEY `academic_assistants_deleted_by_foreign` (`deleted_by`);

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
-- Indexes for table `converted_leads`
--
ALTER TABLE `converted_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `converted_leads_lead_id_foreign` (`lead_id`),
  ADD KEY `converted_leads_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `countries_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_team_id_foreign` (`team_id`),
  ADD KEY `leads_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_activities_lead_id_foreign` (`lead_id`),
  ADD KEY `lead_activities_lead_status_id_foreign` (`lead_status_id`),
  ADD KEY `lead_activities_created_by_foreign` (`created_by`),
  ADD KEY `lead_activities_updated_by_foreign` (`updated_by`),
  ADD KEY `lead_activities_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_sources_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_statuses_deleted_by_foreign` (`deleted_by`);

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
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_created_by_foreign` (`created_by`),
  ADD KEY `notifications_role_id_foreign` (`role_id`);

--
-- Indexes for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_reads_notification_id_user_id_unique` (`notification_id`,`user_id`),
  ADD KEY `notification_reads_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`),
  ADD KEY `settings_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teams_team_lead_id_foreign` (`team_lead_id`),
  ADD KEY `teams_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_roles_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `voxbay_call_logs`
--
ALTER TABLE `voxbay_call_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voxbay_call_logs_created_by_foreign` (`created_by`),
  ADD KEY `voxbay_call_logs_updated_by_foreign` (`updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_assistants`
--
ALTER TABLE `academic_assistants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `converted_leads`
--
ALTER TABLE `converted_leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `lead_activities`
--
ALTER TABLE `lead_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notification_reads`
--
ALTER TABLE `notification_reads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `voxbay_call_logs`
--
ALTER TABLE `voxbay_call_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_assistants`
--
ALTER TABLE `academic_assistants`
  ADD CONSTRAINT `academic_assistants_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `academic_assistants_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `academic_assistants_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `converted_leads`
--
ALTER TABLE `converted_leads`
  ADD CONSTRAINT `converted_leads_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `converted_leads_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `countries`
--
ALTER TABLE `countries`
  ADD CONSTRAINT `countries_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leads_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);

--
-- Constraints for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD CONSTRAINT `lead_activities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_activities_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lead_activities_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lead_activities_lead_status_id_foreign` FOREIGN KEY (`lead_status_id`) REFERENCES `lead_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lead_activities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD CONSTRAINT `lead_sources_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  ADD CONSTRAINT `lead_statuses_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD CONSTRAINT `notification_reads_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teams_team_lead_id_foreign` FOREIGN KEY (`team_lead_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `voxbay_call_logs`
--
ALTER TABLE `voxbay_call_logs`
  ADD CONSTRAINT `voxbay_call_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `voxbay_call_logs_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
