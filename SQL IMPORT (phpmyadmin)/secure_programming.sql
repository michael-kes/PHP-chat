-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 27 jun 2019 om 14:56
-- Serverversie: 10.1.39-MariaDB
-- PHP-versie: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secure_programming`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sw_chat`
--

CREATE TABLE `sw_chat` (
  `chat_id` int(11) NOT NULL,
  `chat_message` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `timemessage` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `sw_chat`
--

INSERT INTO `sw_chat` (`chat_id`, `chat_message`, `user_id`, `group_id`, `timemessage`) VALUES
(41, 'VGVzdA==', 6, 54661, '2017-01-27 19:54:58'),
(42, 'dGVzdA==', 6, 54661, '2017-01-31 01:48:30');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sw_group`
--

CREATE TABLE `sw_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(45) NOT NULL,
  `group_description` text NOT NULL,
  `group_password` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `sw_group`
--

INSERT INTO `sw_group` (`group_id`, `group_name`, `group_description`, `group_password`, `created_by`) VALUES
(54663, 'Testgroep', 'Test', '$6$rounds=7000$5d14b99c8e755$3VqzC2wki7eWWiGooDGxC7.LR1fMmfTFJWTicO5t5KUp6T6gs5skets6w9kwF55c4kVhXEjFqg0WHnPxmydiA1', 'Michael');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sw_single_chat`
--

CREATE TABLE `sw_single_chat` (
  `chat_id` int(11) NOT NULL,
  `chat_message` text NOT NULL,
  `user_one_id` int(11) NOT NULL,
  `user_two_id` int(11) NOT NULL,
  `timemessage` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visited` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `sw_single_chat`
--

INSERT INTO `sw_single_chat` (`chat_id`, `chat_message`, `user_one_id`, `user_two_id`, `timemessage`, `visited`) VALUES
(45, 'eW8gZ2Fw', 6, 3, '2017-01-27 20:03:14', b'0'),
(46, 'dGVzdA==', 6, 14, '2017-01-31 01:48:54', b'0'),
(47, 'b2s=', 6, 3, '2017-01-31 01:54:01', b'0'),
(48, 'dGVzdA==', 6, 14, '2017-01-31 15:22:40', b'0');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sw_user`
--

CREATE TABLE `sw_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(45) NOT NULL,
  `user_firstname` varchar(45) NOT NULL,
  `user_lastname` varchar(45) NOT NULL,
  `user_email` varchar(45) NOT NULL,
  `user_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `sw_user`
--

INSERT INTO `sw_user` (`user_id`, `user_name`, `user_firstname`, `user_lastname`, `user_email`, `user_password`) VALUES
(22, 'Michael', 'Michael', 'Kes', 'michaelkes@outlook.com', '$6$rounds=7000$5d14b966a503a$DZ8aL6tvAetXRFfkaV3dxvUgXBM/sL2gqLg.hHNAyWuDEqRhncMl0QOeu8YyPgmGuWVxOwhwMmz7aBDz4O1.7/');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sw_user_group`
--

CREATE TABLE `sw_user_group` (
  `user_group_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_group_rights` int(11) NOT NULL DEFAULT '4'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden geëxporteerd voor tabel `sw_user_group`
--

INSERT INTO `sw_user_group` (`user_group_id`, `group_id`, `user_id`, `user_group_rights`) VALUES
(66, 54660, 3, 1),
(67, 54661, 14, 1),
(77, 54660, 15, 2),
(78, 54660, 16, 2),
(79, 54660, 17, 2),
(80, 54660, 18, 2),
(81, 54660, 19, 2),
(82, 54660, 20, 2),
(83, 54660, 21, 2),
(84, 54661, 6, 2),
(85, 54662, 6, 1),
(86, 54662, 3, 4),
(87, 54663, 22, 1);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `sw_chat`
--
ALTER TABLE `sw_chat`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexen voor tabel `sw_group`
--
ALTER TABLE `sw_group`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexen voor tabel `sw_single_chat`
--
ALTER TABLE `sw_single_chat`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexen voor tabel `sw_user`
--
ALTER TABLE `sw_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexen voor tabel `sw_user_group`
--
ALTER TABLE `sw_user_group`
  ADD PRIMARY KEY (`user_group_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `sw_chat`
--
ALTER TABLE `sw_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT voor een tabel `sw_group`
--
ALTER TABLE `sw_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54664;

--
-- AUTO_INCREMENT voor een tabel `sw_single_chat`
--
ALTER TABLE `sw_single_chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT voor een tabel `sw_user`
--
ALTER TABLE `sw_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT voor een tabel `sw_user_group`
--
ALTER TABLE `sw_user_group`
  MODIFY `user_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
