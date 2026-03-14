-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 07:25 AM
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
-- Database: `wallet_app`
--

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
(4, '2026_03_01_065237_create_transactions_table', 1),
(5, '2026_03_01_065329_create_personal_access_tokens_table', 1),
(6, '2026_03_05_125408_add_avatar_to_users_table', 1),
(7, '2026_03_06_213800_add_role_to_users_table', 2),
(8, '2026_03_06_213848_add_status_to_transactions_table', 2);

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
  `name` text NOT NULL,
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
(16, 'App\\Models\\User', 12, 'auth_token', 'b3fe39b40f1fee873b83ee7449f6678aca5fac3f3d0423379169b0414f793125', '[\"*\"]', '2026-03-05 16:19:38', NULL, '2026-03-05 16:19:23', '2026-03-05 16:19:38'),
(32, 'App\\Models\\User', 14, 'auth_token', 'f283c803c2296280e188c42f8c8ce4ab4390df338c975826f42577e3b5826180', '[\"*\"]', '2026-03-06 15:59:36', NULL, '2026-03-06 15:59:22', '2026-03-06 15:59:36'),
(36, 'App\\Models\\User', 15, 'auth_token', 'b9a98ccf00b0320a4797f97179c3bdd1ef74e3cbeac8b9e6e649f4dd2f3c2b3e', '[\"*\"]', '2026-03-06 20:41:32', NULL, '2026-03-06 20:31:51', '2026-03-06 20:41:32'),
(37, 'App\\Models\\User', 13, 'auth_token', 'ba1889f311eeccff9a827de21c70c81727c8a13b843ca1a6532bdd9086c883c9', '[\"*\"]', '2026-03-06 20:51:53', NULL, '2026-03-06 20:42:16', '2026-03-06 20:51:53');

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
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('topup','transfer_in','transfer_out') NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `amount` bigint(20) UNSIGNED NOT NULL,
  `balance_before` bigint(20) UNSIGNED NOT NULL,
  `balance_after` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `sender_id`, `receiver_id`, `type`, `status`, `amount`, `balance_before`, `balance_after`, `description`, `reference_id`, `created_at`, `updated_at`) VALUES
(1, 12, NULL, 12, 'topup', 'approved', 5000, 0, 5000, 'Top up saldo', '58afefcd-796e-4188-95cf-ffc34f14adcf', '2026-03-05 15:49:01', '2026-03-05 15:49:01'),
(2, 12, NULL, 12, 'topup', 'approved', 150000, 5000, 155000, 'Top up saldo', 'bf33fb23-9e68-40bd-a5c5-213f6d82596e', '2026-03-05 15:49:21', '2026-03-05 15:49:21'),
(3, 12, 12, 13, 'transfer_out', 'approved', 45000, 155000, 110000, 'Transfer ke raflierlangga', '2e468460-1945-48ae-a8f8-0b53062160c6-out', '2026-03-05 15:56:06', '2026-03-05 15:56:06'),
(4, 13, 12, 13, 'transfer_in', 'approved', 45000, 0, 45000, 'Transfer dari erlangga', '2e468460-1945-48ae-a8f8-0b53062160c6-in', '2026-03-05 15:56:06', '2026-03-05 15:56:06'),
(5, 12, 12, 13, 'transfer_out', 'approved', 50000, 110000, 60000, 'Transfer ke raflierlangga', '802de805-6c14-46de-b4b6-bc68f7895713-out', '2026-03-05 16:19:38', '2026-03-05 16:19:38'),
(6, 13, 12, 13, 'transfer_in', 'approved', 50000, 45000, 95000, 'Transfer dari erlangga', '802de805-6c14-46de-b4b6-bc68f7895713-in', '2026-03-05 16:19:38', '2026-03-05 16:19:38'),
(7, 13, 13, 12, 'transfer_out', 'approved', 45000, 95000, 50000, 'Transfer ke erlangga', 'a2949807-faf4-47f6-a3af-797db704988a-out', '2026-03-05 16:21:59', '2026-03-05 16:21:59'),
(8, 12, 13, 12, 'transfer_in', 'approved', 45000, 60000, 105000, 'Transfer dari raflierlangga', 'a2949807-faf4-47f6-a3af-797db704988a-in', '2026-03-05 16:21:59', '2026-03-05 16:21:59'),
(9, 13, NULL, 13, 'topup', 'approved', 250000, 50000, 300000, 'Top up saldo', '5afa930b-d1ba-4117-a5b6-0f12e004703e', '2026-03-05 16:27:30', '2026-03-05 16:27:30'),
(10, 13, 13, 14, 'transfer_out', 'approved', 220000, 300000, 80000, 'Transfer ke dyah23', 'e92a7e2c-3aa3-48fa-9e99-d913166f4e79-out', '2026-03-05 16:28:02', '2026-03-05 16:28:02'),
(11, 14, 13, 14, 'transfer_in', 'approved', 220000, 0, 220000, 'Transfer dari raflierlangga', 'e92a7e2c-3aa3-48fa-9e99-d913166f4e79-in', '2026-03-05 16:28:02', '2026-03-05 16:28:02'),
(12, 14, 14, 12, 'transfer_out', 'approved', 20000, 220000, 200000, 'Transfer ke erlangga', '1cf8540a-8d09-4477-a15e-80ed1135a13e-out', '2026-03-05 16:28:36', '2026-03-05 16:28:36'),
(13, 12, 14, 12, 'transfer_in', 'approved', 20000, 105000, 125000, 'Transfer dari dyah23', '1cf8540a-8d09-4477-a15e-80ed1135a13e-in', '2026-03-05 16:28:36', '2026-03-05 16:28:36'),
(14, 13, NULL, 13, 'topup', 'approved', 200000, 80000, 280000, 'Top Up saldo', 'b33a62bb-c4d3-4c64-9075-7bef3154fd6a', '2026-03-06 15:34:35', '2026-03-06 15:35:04'),
(15, 13, NULL, 13, 'topup', 'approved', 500000, 280000, 780000, 'Top Up saldo', '820de358-eb9e-4525-b533-86d402448df3', '2026-03-06 15:35:38', '2026-03-06 15:36:10'),
(16, 13, 13, 14, 'transfer_out', 'approved', 125000, 780000, 655000, 'Transfer ke dyah23', 'f7eec003-d17b-4d90-9b54-627ce9ca5980-out', '2026-03-06 15:36:55', '2026-03-06 15:36:55'),
(17, 14, 13, 14, 'transfer_in', 'approved', 125000, 200000, 325000, 'Transfer dari raflierlangga', 'f7eec003-d17b-4d90-9b54-627ce9ca5980-in', '2026-03-06 15:36:55', '2026-03-06 15:36:55'),
(18, 14, NULL, 14, 'topup', 'approved', 200000, 325000, 525000, 'Top Up saldo', '63857e18-bd91-4a78-a6cf-ab312a5111a1', '2026-03-06 15:59:36', '2026-03-06 16:14:23'),
(19, 13, NULL, 13, 'topup', 'pending', 50000, 655000, 655000, 'Top Up saldo - menunggu konfirmasi', '43413888-5c68-4220-bf94-3809eae3886f', '2026-03-06 16:04:35', '2026-03-06 16:04:35'),
(20, 13, 13, 12, 'transfer_out', 'approved', 200000, 655000, 455000, 'Transfer ke erlangga', '1a65b219-1fd8-48d2-91d1-7c4e96816c9e-out', '2026-03-06 16:08:49', '2026-03-06 16:08:49'),
(21, 12, 13, 12, 'transfer_in', 'approved', 200000, 125000, 325000, 'Transfer dari raflierlangga', '1a65b219-1fd8-48d2-91d1-7c4e96816c9e-in', '2026-03-06 16:08:49', '2026-03-06 16:08:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `balance` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `avatar`, `email_verified_at`, `password`, `balance`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(12, 'Erlangga', 'erlangga', 'erlangga@mail.com', '0812817289164', '1772750463_69aa067fa19d9.jpg', '2026-03-05 14:20:30', '$2y$12$9XKi06JY7mDYYTKPWP/fsOT1Ed8eQK0NKXQ.K1frxsB./boVj8PVa', 325000, 'user', NULL, '2026-03-05 14:20:30', '2026-03-06 16:08:49'),
(13, 'Rafli Erlangga', 'raflierlangga', 'rafli@mail.com', '089652526133', '1772854947_69ab9ea3c129e.jpg', NULL, '$2y$12$YBjb3gSgF7PCUZSBXPOQW.LgdEE8QwGnaQyc9765TjQr0ti9Nidku', 455000, 'user', NULL, '2026-03-05 15:49:51', '2026-03-06 20:42:27'),
(14, 'Dyah Nisa', 'dyah23', 'dyah@mail.com', NULL, NULL, NULL, '$2y$12$6Mjj4Me0IlgBcwNLMPg5RecWCnZt/eVGR34U1QZuEgykArG5awD0u', 525000, 'user', NULL, '2026-03-05 16:07:36', '2026-03-06 16:14:23'),
(15, 'Anggaa', 'admin1', 'admin@mail.com', NULL, NULL, NULL, '$2y$12$lQVrZ0TmcUvZ1L4.02TOke8bcQS6rZLdm2o/JossOqBRbP1Cws0Rq', 0, 'admin', NULL, '2026-03-06 15:32:57', '2026-03-06 20:41:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_reference_id_unique` (`reference_id`),
  ADD KEY `transactions_sender_id_foreign` (`sender_id`),
  ADD KEY `transactions_receiver_id_foreign` (`receiver_id`),
  ADD KEY `transactions_user_id_index` (`user_id`),
  ADD KEY `transactions_reference_id_index` (`reference_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
