-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Vært: localhost
-- Genereringstid: 15. 12 2024 kl. 23:23:54
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
-- Struktur-dump for tabellen `emails`
--

CREATE TABLE `emails` (
  `id` int NOT NULL,
  `sender` varchar(255) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `files`
--

INSERT INTO `files` (`id`, `file_name`, `content`, `folder_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Letter_from_Doctor_Stanislaus_Braun', 'A Letter to the Overseer from Dr. Stanislaus Braun:\r\n\r\nIf you are reading this, emergency Vault \r\ninternmentprocedures have been initiated and you and your control group have been sealed into your Vault. Congratulations! You are now a vital part of the most ambitious program ever undertaken by Vault-Tec.\r\n \r\nIf you have not yet read your sealed orders, do so now. They will outline the experimental protocols assigned to your control group. Please remember \r\nthat deviation from these protocols in any way will jeopardize the success of the program, and may be considered grounds for termination by Vault-Tec Corporation (as outlined in your \r\nEmployment Agreement)\r\n \r\nYour Vault may or may not have been selected to receive a G.E.C.K. module. \r\nPlease see Attachment A for details.\r\n \r\nDoctor Stanislaus Braun\r\nDirector, Societal Preservation Program\r\nVault-Tec Corporation', NULL, NULL, NULL, NULL);

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
(1, 'help', '[cmd]', 'shows info about command', 1, 1, 1, 1),
(2, 'uplink', '<access code>', 'connect to ARPANET', 0, 0, 1, 0),
(3, 'version', '{}', 'UOS version', 1, 1, 1, 1),
(4, 'color', '<GREEN|WHITE|YELLOW>', 'terminal color', 0, 0, 0, 0),
(5, 'newuser', '<username>', 'create ARPANET account', 0, 0, 1, 0),
(6, 'login', '<username>', 'auth ARPANET user', 0, 0, 1, 1),
(7, 'logout', '{}', 'logout ARPANET user', 1, 1, 0, 1),
(8, 'telnet', '<host>', 'connect to host', 1, 1, 0, 1),
(9, 'ls', '{}', 'list files on host', 0, 1, 0, 0),
(10, 'cat', '<filename>', 'display file content', 0, 1, 0, 0),
(11, 'mail', '<subject> <user> < <body>', 'email user', 1, 1, 0, 1),
(12, 'user', '{}', 'list user info', 1, 1, 0, 1),
(13, 'scan', '{}', 'list connected nodes', 1, 1, 0, 1),
(14, 'set', '<command>', 'TERMINAL/INQUIRE, FILE/PROTECTION=OWNER:RWED ACCOUNTS.F, HALT RESTART/MAINT', 0, 0, 0, 1),
(15, 'run', '<command>', 'DEBUG/ACCOUNTS.F', 0, 0, 0, 1),
(16, 'mem', '[dump]', 'run memory dump', 0, 0, 0, 1);

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
(1, 1, 'robco', 'milnet', 'US Military (PRIVATE)', '1.1.1.1', 1, 5, '1984-10-22 16:18:50', NULL),
(2, 1, 'robco', 'nsfnet', 'Academic Research (PUBLIC)', '0.0.0.0', 1, 1, '1969-10-10 16:29:25', NULL),
(3, 1, 'robco', 'ucla', 'University of California, LA', '0.0.0.1', 1, 1, '1985-10-22 16:18:50', NULL),
(4, 1, 'robco', 'arc', 'Stanford Research Institute', '0.0.0.2', 1, 1, '1985-10-22 16:18:50', NULL),
(5, 1, 'robco', 'ucsb', 'University of California, SB ', '0.0.0.3', 1, 1, '1985-10-22 16:18:50', NULL),
(6, 1, 'robco', 'uusc', 'University of Utah School of Computing', '0.0.0.4', 1, 1, '1985-10-22 16:18:50', NULL),
(7, 1, 'robco', 'spsdd', 'Seattle Public School District Datanet', '0.0.0.5', 1, 1, '1985-10-22 16:18:50', NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `host_file`
--

CREATE TABLE `host_file` (
  `id` bigint UNSIGNED NOT NULL,
  `file_id` int NOT NULL,
  `host_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `host_file`
--

INSERT INTO `host_file` (`id`, `file_id`, `host_id`) VALUES
(1, 1, 7);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `host_node`
--

CREATE TABLE `host_node` (
  `id` bigint UNSIGNED NOT NULL,
  `node_id` int NOT NULL,
  `host_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `host_node`
--

INSERT INTO `host_node` (`id`, `node_id`, `host_id`) VALUES
(1, 3, 2),
(2, 4, 2),
(3, 5, 2),
(4, 6, 2),
(5, 7, 2);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `host_user`
--

CREATE TABLE `host_user` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `host_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `host_user`
--

INSERT INTO `host_user` (`id`, `user_id`, `host_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7);

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
-- Struktur-dump for tabellen `missions`
--

CREATE TABLE `missions` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `trigger_event` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `rewards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` enum('inactive','active','completed') COLLATE utf8mb4_general_ci DEFAULT 'inactive',
  `next_mission_id` int DEFAULT NULL,
  `email_id` int DEFAULT NULL
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
(3, 'guest@teleterm.net', 'guest', NULL, '371464-1Z901A-Z9X663-YXY9Z6', 'Slaughter', 'Wigglesworth', NULL, 1, 0, 0, 'UNKNOWN', NULL, '2024-11-20 16:20:17', '2024-11-20 16:20:17');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_missions`
--

CREATE TABLE `user_missions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `mission_id` int NOT NULL,
  `status` enum('inactive','active','completed') DEFAULT 'inactive',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indeks for tabel `host_file`
--
ALTER TABLE `host_file`
  ADD PRIMARY KEY (`id`);

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
-- Indeks for tabel `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_id` (`email_id`);

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`user_name`);

--
-- Indeks for tabel `user_missions`
--
ALTER TABLE `user_missions`
  ADD PRIMARY KEY (`id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `emails`
--
ALTER TABLE `emails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tilføj AUTO_INCREMENT i tabel `hosts`
--
ALTER TABLE `hosts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tilføj AUTO_INCREMENT i tabel `host_file`
--
ALTER TABLE `host_file`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `host_node`
--
ALTER TABLE `host_node`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `host_user`
--
ALTER TABLE `host_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Tilføj AUTO_INCREMENT i tabel `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tilføj AUTO_INCREMENT i tabel `user_missions`
--
ALTER TABLE `user_missions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `folders_ibfk_3` FOREIGN KEY (`host_id`) REFERENCES `hosts` (`id`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `missions`
--
ALTER TABLE `missions`
  ADD CONSTRAINT `missions_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `emails` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
