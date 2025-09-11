-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 11, 2025 at 08:48 AM
-- Server version: 8.0.41
-- PHP Version: 8.1.33

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
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fees` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
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
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` text COLLATE utf8mb4_unicode_ci,
  `country_id` bigint UNSIGNED DEFAULT NULL,
  `interest_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lead_status_id` bigint UNSIGNED DEFAULT NULL,
  `lead_source_id` bigint UNSIGNED DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `telecaller_id` bigint UNSIGNED DEFAULT NULL,
  `team_id` bigint UNSIGNED DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `course_id` bigint UNSIGNED DEFAULT NULL,
  `by_meta` tinyint(1) NOT NULL DEFAULT '0',
  `meta_lead_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_converted` tinyint(1) NOT NULL DEFAULT '0',
  `followup_date` date DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_activities`
--

CREATE TABLE `lead_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_sources`
--

CREATE TABLE `lead_sources` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_sources`
--

INSERT INTO `lead_sources` (`id`, `title`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Website', 'Leads from website', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(2, 'Social Media', 'Leads from social media', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(3, 'Referral', 'Leads from referrals', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(4, 'Cold Call', 'Leads from cold calling', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(5, 'Email Campaign', 'Leads from email campaigns', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(6, 'Advertisement', 'Leads from advertisements', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(7, 'Walk-in', 'Walk-in leads', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(8, 'Other', 'Other sources', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lead_statuses`
--

CREATE TABLE `lead_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_statuses`
--

INSERT INTO `lead_statuses` (`id`, `title`, `description`, `color`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'New Lead', 'Newly created lead', '#28a745', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(2, 'Contacted', 'Lead has been contacted', '#17a2b8', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(3, 'Interested', 'Lead is interested', '#ffc107', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(4, 'Not Interested', 'Lead is not interested', '#dc3545', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(5, 'Follow Up', 'Lead needs follow up', '#6f42c1', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(6, 'Converted', 'Lead has been converted', '#20c997', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL),
(7, 'Lost', 'Lead is lost', '#6c757d', 1, '2025-09-10 05:48:24', '2025-09-10 05:48:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(14, '2025_09_11_054906_create_settings_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('33kkYE3gy6k5i9yvpICeP2OBvLWC1ybdyNvstyuc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfdG9rZW4iO3M6NDA6InBLb1hJQ0FtQTdUa09PTk5RQmRIRGV2Ym5rZ0RiajhEZkJ3NGgzb2wiO3M6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM0OiJodHRwczovL2Jhc2UtY3JtLnRlc3QvbGVhZHMvY3JlYXRlIjt9czo3OiJ1c2VyX2lkIjtpOjE7czoxMjoiaXNfdGVhbV9sZWFkIjtiOjA7czoxNToiaXNfdGVhbV9tYW5hZ2VyIjtiOjA7czo3OiJyb2xlX2lkIjtpOjE7czoxMDoicm9sZV90aXRsZSI7czo1OiJBZG1pbiI7czo5OiJ1c2VyX25hbWUiO3M6MTA6IkFkbWluIFVzZXIiO3M6MTA6InVzZXJfZW1haWwiO3M6MTM6ImFkbWluQGNybS5jb20iO3M6MTI6ImlzX2xvZ2dlZF9pbiI7YjoxO3M6MTI6ImxvZ2dlZF9pbl9hdCI7aToxNzU3NTA0Mjc2O30=', 1757507514),
('bPT1On1PR3YmEOUzn2sAupLZve3b1Ohojt4DPvYf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfdG9rZW4iO3M6NDA6InJMWHY3WVNjSHBtRHZzdWhUTno5VUFiNkdwZGNNS2xPcEZtTkhRUWsiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwczovL2Jhc2UtY3JtLnRlc3QvbGVhZHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjc6InVzZXJfaWQiO2k6MTtzOjEyOiJpc190ZWFtX2xlYWQiO2I6MDtzOjE1OiJpc190ZWFtX21hbmFnZXIiO2I6MDtzOjc6InJvbGVfaWQiO2k6MTtzOjEwOiJyb2xlX3RpdGxlIjtzOjU6IkFkbWluIjtzOjk6InVzZXJfbmFtZSI7czoxMDoiQWRtaW4gVXNlciI7czoxMDoidXNlcl9lbWFpbCI7czoxMzoiYWRtaW5AY3JtLmNvbSI7czoxMjoiaXNfbG9nZ2VkX2luIjtiOjE7czoxMjoibG9nZ2VkX2luX2F0IjtpOjE3NTc1MDMyOTQ7fQ==', 1757503660),
('eUO2k0SaVJg2jf70kd6aD0nbmO1PvZwalPuqNUxu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo2OiJfdG9rZW4iO3M6NDA6IjZEdllNUmduVEt1cUZRYndPRGR1dWpjQWZaeFJBOVYzTEtFUDVIc3AiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwczovL2Jhc2UtY3JtLnRlc3QvYWRtaW4vbGVhZC1zdGF0dXNlcyI7fXM6NzoidXNlcl9pZCI7aToxO3M6MTI6ImlzX3RlYW1fbGVhZCI7YjowO3M6MTU6ImlzX3RlYW1fbWFuYWdlciI7YjowO3M6Nzoicm9sZV9pZCI7aToxO3M6MTA6InJvbGVfdGl0bGUiO3M6NToiQWRtaW4iO3M6OToidXNlcl9uYW1lIjtzOjEwOiJBZG1pbiBVc2VyIjtzOjEwOiJ1c2VyX2VtYWlsIjtzOjEzOiJhZG1pbkBjcm0uY29tIjtzOjEyOiJpc19sb2dnZWRfaW4iO2I6MTtzOjEyOiJsb2dnZWRfaW5fYXQiO2k6MTc1NzU2NDE2MTt9', 1757576170),
('MSnNNi4cmgbkkTttSdB8HXKOENojVPGOJfxJbDoS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidEZicGVWajVMV1dmS1JTSXo4cVl6NFZPMGlVWGV0VEhySlNCeUFCUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHBzOi8vYmFzZS1jcm0udGVzdCI7fX0=', 1757567020);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `group`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Base CRM', 'text', 'Website name', 'general', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(2, 'site_description', 'Customer Relationship Management System', 'text', 'Website description', 'general', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(3, 'site_logo', '', 'file', 'Website logo', 'general', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(4, 'contact_phone', '+1-234-567-8900', 'text', 'Contact phone number', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(5, 'contact_email', 'info@basecrm.com', 'text', 'Contact email address', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(6, 'contact_address', '123 Business Street, City, State 12345', 'text', 'Contact address', 'contact', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(7, 'email_from_name', 'Base CRM', 'text', 'Email sender name', 'email', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(8, 'email_from_address', 'noreply@basecrm.com', 'text', 'Email sender address', 'email', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(9, 'facebook_url', '', 'text', 'Facebook page URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(10, 'twitter_url', '', 'text', 'Twitter profile URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(11, 'linkedin_url', '', 'text', 'LinkedIn profile URL', 'social', 1, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(12, 'timezone', 'UTC', 'text', 'System timezone', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(13, 'date_format', 'Y-m-d', 'text', 'Date format', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51'),
(14, 'time_format', 'H:i:s', 'text', 'Time format', 'system', 0, '2025-09-11 00:24:51', '2025-09-11 00:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `team_lead_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `team_lead_id`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Team 1', 'team 1', NULL, 1, NULL, '2025-09-11 01:07:33', '2025-09-11 01:07:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `is_team_lead` tinyint(1) NOT NULL DEFAULT '0',
  `is_team_manager` tinyint(1) NOT NULL DEFAULT '0',
  `current_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `code`, `otp`, `profile_picture`, `role_id`, `is_team_lead`, `is_team_manager`, `current_role`, `team_id`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@crm.com', NULL, '$2y$12$s1J6SzGP692lMklJZBwe2O7y7H8V12M6v3z.P3jFruJqBRZWYPZdS', NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, '2025-09-10 05:48:27', '2025-09-10 05:48:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `title`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'Full system access with all permissions', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL),
(2, 'Admin', 'Administrative access with user management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL),
(3, 'Telecaller', 'Telecaller access for lead management', 1, '2025-09-11 01:00:13', '2025-09-11 01:00:13', NULL);

--
-- Indexes for dumped tables
--

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
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `leads_team_id_foreign` (`team_id`);

--
-- Indexes for table `lead_activities`
--
ALTER TABLE `lead_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_sources`
--
ALTER TABLE `lead_sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teams_team_lead_id_foreign` (`team_lead_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_activities`
--
ALTER TABLE `lead_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_sources`
--
ALTER TABLE `lead_sources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_team_lead_id_foreign` FOREIGN KEY (`team_lead_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
