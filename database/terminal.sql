-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Vært: localhost
-- Genereringstid: 03. 12 2024 kl. 23:30:31
-- Serverversion: 8.0.39
-- PHP-version: 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `terminal`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `files`
--

CREATE TABLE `files` (
  `id` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `folder_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `host_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `files`
--

INSERT INTO `files` (`id`, `file_name`, `content`, `folder_id`, `user_id`, `host_id`, `created_at`, `updated_at`) VALUES
(1, 'Letter from Doctor Stanislaus Braun', 'A Letter to the Overseer from Dr. Stanislaus Braun:\r\n\r\nIf you are reading this, emergency Vault \r\ninternmentprocedures have been initiated and you \r\nand your control group have been sealed into \r\nyour Vault. Congratulations! You are now a vital \r\npart of the most ambitious program ever undertaken \r\nby Vault-Tec.\r\n \r\nIf you have not yet read your sealed orders, do so \r\nnow. They will outline the experimental protocols \r\nassigned to your control group. Please remember \r\nthat deviation from these protocols in any way \r\nwill jeopardize the success of the program, \r\nand may be considered grounds for termination \r\nby Vault-Tec Corporation (as outlined in your \r\nEmployment Agreement)\r\n \r\nYour Vault may or may not have been selected \r\nto receive a G.E.C.K. module. \r\nPlease see Attachment A for details.\r\n \r\nDoctor Stanislaus Braun\r\nDirector, Societal Preservation Program\r\nVault-Tec Corporation', NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `folders`
--

CREATE TABLE `folders` (
  `id` bigint UNSIGNED NOT NULL,
  `folder_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `host_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `help`
--

CREATE TABLE `help` (
  `id` bigint UNSIGNED NOT NULL,
  `cmd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `input` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_user` tinyint(1) DEFAULT '0',
  `is_host` tinyint(1) DEFAULT '0',
  `is_visitor` tinyint(1) DEFAULT '0',
  `is_guest` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `help`
--

INSERT INTO `help` (`id`, `cmd`, `input`, `info`, `is_user`, `is_host`, `is_visitor`, `is_guest`) VALUES
(1, 'help', '[cmd]', 'shows info about command', 0, 0, 1, 0),
(2, 'uplink', '<access code>', 'connect to ARPANET', 0, 0, 1, 0),
(3, 'version', '{}', 'UOS version', 1, 1, 1, 1),
(4, 'color', '<GREEN|WHITE|YELLOW>', 'terminal color', 0, 0, 0, 0),
(5, 'newuser', '<username>', 'create ARPANET account', 0, 0, 1, 0),
(6, 'login', '<username>', 'auth ARPANET user', 0, 0, 1, 1),
(7, 'logout', '{}', 'logout ARPANET user', 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `hosts`
--

CREATE TABLE `hosts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'robco',
  `host_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `org` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `level_id` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `hosts`
--

INSERT INTO `hosts` (`id`, `user_id`, `password`, `host_name`, `org`, `ip`, `active`, `level_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'robco', 'milnet', 'Department of Defense', '1.1.1.1', 1, 5, '1984-10-22 16:18:50', NULL),
(2, 1, 'robco', 'arpanet', 'Department of Defense', '0.0.0.0', 1, 1, '1969-10-10 16:29:25', NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `host_node`
--

CREATE TABLE `host_node` (
  `id` bigint UNSIGNED NOT NULL,
  `node_id` int NOT NULL,
  `host_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `host_user`
--

CREATE TABLE `host_user` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `host_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `levels`
--

CREATE TABLE `levels` (
  `id` bigint UNSIGNED NOT NULL,
  `rep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xp_req` int NOT NULL DEFAULT '0',
  `xp_reward` int NOT NULL DEFAULT '0',
  `min` int NOT NULL DEFAULT '3',
  `max` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `levels`
--

INSERT INTO `levels` (`id`, `rep`, `xp_req`, `xp_reward`, `min`, `max`) VALUES
(0, 'NONE', 0, 1, 2, 3),
(1, 'Novice', 15, 2, 4, 5),
(2, 'Skilled', 25, 3, 6, 8),
(3, 'Advanced', 50, 4, 8, 10),
(4, 'Expert', 75, 5, 10, 12),
(5, 'Master', 100, 10, 12, 15);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `logs`
--

CREATE TABLE `logs` (
  `id` bigint UNSIGNED NOT NULL,
  `host_id` int NOT NULL,
  `info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `access_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `level_id` int NOT NULL DEFAULT '0',
  `xp` int NOT NULL DEFAULT '0',
  `rep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNKNOWN',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `users`
--

INSERT INTO `users` (`id`, `email`, `user_name`, `password`, `access_code`, `firstname`, `lastname`, `role`, `active`, `level_id`, `xp`, `rep`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'sysadmin@teleterm.net', 'sysadmin', 'robco', 'Z62749-9XZZ9A-1A0YZ6-773Y1A', 'System', 'Admin', NULL, 1, 5, 100, 'MASTER', NULL, NULL, NULL),
(2, 'admin@teleterm.net', 'admin', 'robco', 'Z62749-9XZZ9A-1A0YZ6-773Y1A', 'Host', 'Admin', NULL, 1, 5, 100, 'MASTER', NULL, NULL, NULL),
(3, NULL, 'thom855j', 'c.m.lange', '371464-1Z901A-Z9X663-YXY9Z6', 'Slaughter', 'Wigglesworth', NULL, 1, 0, 0, 'UNKNOWN', NULL, '2024-11-20 16:20:17', '2024-11-20 16:20:17');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `host_id` (`host_id`);

--
-- Indeks for tabel `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `host_id` (`host_id`);

--
-- Indeks for tabel `help`
--
ALTER TABLE `help`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `hosts`
--
ALTER TABLE `hosts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `host_node`
--
ALTER TABLE `host_node`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `host_user`
--
ALTER TABLE `host_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`user_name`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `folders`
--
ALTER TABLE `folders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `help`
--
ALTER TABLE `help`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tilføj AUTO_INCREMENT i tabel `hosts`
--
ALTER TABLE `hosts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `host_node`
--
ALTER TABLE `host_node`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `host_user`
--
ALTER TABLE `host_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `levels`
--
ALTER TABLE `levels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tilføj AUTO_INCREMENT i tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_3` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
