-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 18, 2024 at 02:08 AM
-- Server version: 5.7.29
-- PHP Version: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sfadtech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `sf_offers`
--

CREATE TABLE `sf_offers` (
  `id` int(11) NOT NULL,
  `advertiser_id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL,
  `cost_per_click` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `theme` varchar(225) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_offers`
--

INSERT INTO `sf_offers` (`id`, `advertiser_id`, `name`, `cost_per_click`, `url_id`, `is_active`, `theme`, `timestamp`) VALUES
(5, 2, 'Млечного Пути', 23, 1, 1, 'блог', '2024-03-17 22:28:12'),
(6, 2, 'Чернобелый экран телефона', 23, 1, 1, 'блог', '2024-03-17 22:28:15'),
(7, 2, 'Maho Beach', 34, 2, 1, 'WEBCAM ', '2024-03-17 22:31:10'),
(8, 2, 'Фильм COLUMBUS', 345, 3, 1, 'блог', '2024-03-17 22:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `sf_offers_to_webmaster`
--

CREATE TABLE `sf_offers_to_webmaster` (
  `id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `webmaster_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_offers_to_webmaster`
--

INSERT INTO `sf_offers_to_webmaster` (`id`, `offer_id`, `webmaster_id`, `is_active`, `timestamp`) VALUES
(1, 6, 3, 1, '2024-03-17 22:33:02'),
(2, 7, 3, 1, '2024-03-17 22:33:19'),
(3, 5, 3, 1, '2024-03-17 22:34:44'),
(4, 8, 3, 1, '2024-03-17 22:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `sf_redirect_logs`
--

CREATE TABLE `sf_redirect_logs` (
  `id` int(11) NOT NULL,
  `webmaster_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `url_id` int(11) NOT NULL,
  `redirect_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price_taken` varchar(225) DEFAULT NULL,
  `redirected` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_redirect_logs`
--

INSERT INTO `sf_redirect_logs` (`id`, `webmaster_id`, `offer_id`, `url_id`, `redirect_date`, `price_taken`, `redirected`, `ip`) VALUES
(1, 3, 5, 1, '2024-03-17 22:35:09', '23', 1, '127.0.0.1'),
(2, 3, 6, 1, '2024-03-17 22:38:09', '23', 1, '127.0.0.1'),
(3, 3, 6, 1, '2024-03-17 22:38:20', '23', 1, '127.0.0.1'),
(4, 3, 5, 1, '2024-03-17 22:38:29', '23', 1, '127.0.0.1'),
(5, 3, 7, 2, '2024-03-17 22:39:20', '34', 1, '127.0.0.1'),
(6, 3, 6, 1, '2024-03-17 22:39:26', '23', 1, '127.0.0.1'),
(7, 3, 8, 3, '2024-03-17 22:39:30', '345', 1, '127.0.0.1'),
(8, 3, 5, 1, '2024-03-17 22:42:48', '23', 1, '127.0.0.1'),
(9, 3, 6, 1, '2024-03-17 22:47:56', '23', 1, '127.0.0.1'),
(10, 3, 5, 1, '2024-03-17 22:48:30', '23', 1, '127.0.0.1'),
(11, 3, 7, 2, '2024-03-17 22:49:37', '34', 1, '127.0.0.1'),
(12, 3, 6, 1, '2024-03-17 23:05:29', '23', 1, '127.0.0.1'),
(13, 3, 8, 3, '2024-03-17 23:05:33', '345', 1, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `sf_roles`
--

CREATE TABLE `sf_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_roles`
--

INSERT INTO `sf_roles` (`id`, `name`) VALUES
(1, 'рекламодатель'),
(2, 'веб-мастер'),
(3, 'Админ');

-- --------------------------------------------------------

--
-- Table structure for table `sf_target_urls`
--

CREATE TABLE `sf_target_urls` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_target_urls`
--

INSERT INTO `sf_target_urls` (`id`, `url`) VALUES
(1, 'https://allaboutknit.ru/kakuyu-chasty-mlechnogo-puti-mi-vidim-v-nebe'),
(2, 'https://www.youtube.com/watch?v=LtzkkAeW_Qg'),
(3, 'https://allaboutknit.ru/filym-columbus');

-- --------------------------------------------------------

--
-- Table structure for table `sf_users`
--

CREATE TABLE `sf_users` (
  `id` int(11) NOT NULL,
  `username` varchar(225) NOT NULL,
  `email` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `role_id` int(11) NOT NULL COMMENT '1 рекламодатель или 2 веб-мастер',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sf_users`
--

INSERT INTO `sf_users` (`id`, `username`, `email`, `password`, `role_id`, `is_active`, `timestamp`) VALUES
(1, 'admin', 'admin@admin.local', '$2y$10$6/nfMJyAceepxfJ0346V9eGP6BadBcqOe.pkSqu6tMocR0Bty/3bK', 3, 1, '2024-03-17 22:17:33'),
(2, 'reklama1', 'reklama1@reklama1.loc', '$2y$10$JP1UYNCLs0VH77Te9MztMeuxjylhFpN5OzIHwwSEBahuJ0aslTtma', 1, 1, '2024-03-17 22:19:09'),
(3, 'webmaster1', 'webmaster1@webmaster1.loc', '$2y$10$N48ZROQDWSR5qFDObn7.auQFwSTRpA/2Jz3H.H9.byreR.Us7YCla', 2, 1, '2024-03-17 22:32:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sf_offers`
--
ALTER TABLE `sf_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `url_id` (`url_id`),
  ADD KEY `advertiser_id` (`advertiser_id`);

--
-- Indexes for table `sf_offers_to_webmaster`
--
ALTER TABLE `sf_offers_to_webmaster`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offer_id` (`offer_id`,`webmaster_id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `sf_redirect_logs`
--
ALTER TABLE `sf_redirect_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url_id` (`url_id`),
  ADD KEY `offer_id` (`offer_id`),
  ADD KEY `webmaster_id` (`webmaster_id`),
  ADD KEY `redirected` (`redirected`);

--
-- Indexes for table `sf_roles`
--
ALTER TABLE `sf_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sf_target_urls`
--
ALTER TABLE `sf_target_urls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sf_users`
--
ALTER TABLE `sf_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sf_offers`
--
ALTER TABLE `sf_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sf_offers_to_webmaster`
--
ALTER TABLE `sf_offers_to_webmaster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sf_redirect_logs`
--
ALTER TABLE `sf_redirect_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sf_roles`
--
ALTER TABLE `sf_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sf_target_urls`
--
ALTER TABLE `sf_target_urls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sf_users`
--
ALTER TABLE `sf_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
