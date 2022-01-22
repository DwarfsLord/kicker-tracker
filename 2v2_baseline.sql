-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2022 at 05:35 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elo`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `game_id` int(10) UNSIGNED NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `winner1_id` int(10) UNSIGNED NOT NULL,
  `winner1_old_elo` int(10) UNSIGNED NOT NULL,
  `winner2_id` int(10) UNSIGNED DEFAULT NULL,
  `winner2_old_elo` int(10) UNSIGNED DEFAULT NULL,
  `loser1_id` int(10) UNSIGNED NOT NULL,
  `loser1_old_elo` int(10) UNSIGNED NOT NULL,
  `loser2_id` int(10) UNSIGNED DEFAULT NULL,
  `loser2_old_elo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `player_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(23) NOT NULL,
  `elo1` int(10) UNSIGNED NOT NULL DEFAULT 750,
  `free_elo1` int(11) NOT NULL DEFAULT 250,
  `elo2` int(10) UNSIGNED NOT NULL DEFAULT 750,
  `free_elo2` int(11) NOT NULL DEFAULT 250
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`player_id`, `name`, `elo1`, `free_elo1`, `elo2`, `free_elo2`) VALUES
(1, 'Chaim Lukas', 1000, 0, 1000, 0),
(2, 'The Right Hand of Steve', 1000, 0, 1000, 0),
(3, 'Felix K.', 1000, 0, 1000, 0),
(4, 'The Great Dane', 1000, 0, 1000, 0),
(5, 'Sammy', 1000, 0, 1000, 0),
(6, 'Johannes', 1000, 0, 1000, 0),
(7, 'Zachary Lenhart', 1000, 0, 1000, 0),
(8, 'Sam Girl', 1000, 0, 1000, 0),
(9, 'David Janus', 1000, 0, 1000, 0),
(10, 'Erik', 1000, 0, 1000, 0),
(11, 'A-A-Ron', 1000, 0, 1000, 0),
(12, 'Caleb Karch', 1000, 0, 1000, 0),
(13, 'SwagBry', 1000, 0, 1000, 0),
(14, 'Christian Crebar', 1000, 0, 1000, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `player_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
