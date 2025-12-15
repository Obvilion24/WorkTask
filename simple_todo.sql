-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 02:06 PM
-- Server version: 8.0.44
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simple_todo`
--

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '#7b5dff',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `name`, `description`, `color`, `created_at`) VALUES
(1, 1, 'Personal Task', 'Các công việc cá nhân', '#7b5dff', '2025-11-14 14:41:12'),
(2, 1, 'Office Task', 'Công việc tại văn phòng', '#7b5dff', '2025-11-14 14:41:12'),
(3, 1, 'Daily Study', 'Học tập hằng ngày', '#7b5dff', '2025-11-14 14:41:12'),
(4, 1, 'Quan Thanh', '', '#7b5dff', '2025-11-14 22:52:24'),
(5, 3, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(6, 3, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(7, 3, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(8, 4, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(9, 4, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(10, 4, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(11, 1, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(12, 1, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(13, 1, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(14, 2, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(15, 2, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(16, 2, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(17, 5, 'Office Task', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-14 23:43:46'),
(18, 5, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-14 23:43:46'),
(19, 5, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-14 23:43:46'),
(21, 7, 'Office Task', 'Các dự án công việc tại văn phòng.', '#7b5dff', '2025-11-15 05:20:40'),
(22, 7, 'Personal Task', 'Các dự án và kế hoạch cá nhân.', '#7b5dff', '2025-11-15 05:20:40'),
(23, 7, 'Daily Study', 'Ghi chú học tập hàng ngày.', '#7b5dff', '2025-11-15 05:20:40'),
(24, 8, 'Personal Task', 'Các dự án và kế hoạch cá nhân', '#7b5dff', '2025-11-15 05:26:19'),
(25, 8, 'Office', 'Các dự án công việc tại văn phòng', '#7b5dff', '2025-11-15 05:26:19'),
(26, 8, 'Daily Study', 'Ghi chú học tập hàng ngày', '#7b5dff', '2025-11-15 05:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `group_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `due_date`, `status`, `created_at`, `group_id`) VALUES
(1, 1, 'CÁI gì đó', 'LÀM NGAY ĐI', '2025-01-27', 'completed', '2025-11-12 16:00:01', NULL),
(2, 1, 'a', 'AAA', '2025-10-31', 'completed', '2025-11-13 14:18:42', NULL),
(3, 1, 'B', 'S', '2025-10-30', 'completed', '2025-11-13 14:19:09', NULL),
(6, 1, 'A', 'S', '2025-11-04', 'completed', '2025-11-13 14:29:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_groups`
--

CREATE TABLE `task_groups` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bg_type` enum('video','image') COLLATE utf8mb4_unicode_ci DEFAULT 'video',
  `bg_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/VD.mp4'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `bg_type`, `bg_url`) VALUES
(1, 'trung', '$2y$10$fTTGPNp5celmJTY0ztgme..73LMaycu4TMSf.7IDlVrMyFTgKUq.e', 'trungkith0812@gmail.com', '2025-11-12 15:59:23', 'video', 'assets/VD.mp4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_task_group` (`group_id`);

--
-- Indexes for table `task_groups`
--
ALTER TABLE `task_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `task_groups`
--
ALTER TABLE `task_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_group` FOREIGN KEY (`group_id`) REFERENCES `task_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_groups`
--
ALTER TABLE `task_groups`
  ADD CONSTRAINT `task_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
