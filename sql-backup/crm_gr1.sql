-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 06. Mar, 2026 10:12 AM
-- Tjener-versjon: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm_gr1`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `kontaktpersoner`
--

CREATE TABLE `kontaktpersoner` (
  `kontakt_id` int(11) NOT NULL,
  `kunde_id` int(11) NOT NULL,
  `fornavn` varchar(100) NOT NULL,
  `etternavn` varchar(100) NOT NULL,
  `epost` varchar(100) DEFAULT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `stilling` varchar(100) DEFAULT NULL,
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `kontaktpersoner`
--

INSERT INTO `kontaktpersoner` (`kontakt_id`, `kunde_id`, `fornavn`, `etternavn`, `epost`, `telefon`, `stilling`, `opprettet_dato`) VALUES
(1, 1, 'Lars', 'Hansen', 'lars@nordtech.no', '91234567', 'Daglig leder', '2026-03-06 09:11:26'),
(2, 1, 'Maria', 'Olsen', 'maria@nordtech.no', '92345678', 'Salgssjef', '2026-03-06 09:11:26'),
(3, 2, 'Anders', 'Johansen', 'anders@greensolutions.no', '93456789', 'Prosjektleder', '2026-03-06 09:11:26'),
(4, 2, 'Sofie', 'Nilsen', 'sofie@greensolutions.no', '94567890', 'Økonomisjef', '2026-03-06 09:11:26'),
(5, 3, 'Ola', 'Nordmann', 'ola.nordmann@email.no', '95678901', NULL, '2026-03-06 09:11:26');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `kunder`
--

CREATE TABLE `kunder` (
  `kunde_id` int(11) NOT NULL,
  `kundetype` enum('privat','bedrift') NOT NULL,
  `firmanavn` varchar(100) DEFAULT NULL,
  `organisasjonsnummer` varchar(50) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `kunder`
--

INSERT INTO `kunder` (`kunde_id`, `kundetype`, `firmanavn`, `organisasjonsnummer`, `adresse`, `opprettet_dato`) VALUES
(1, 'bedrift', 'NordTech AS', '912345678', 'Storgata 12, Oslo', '2026-03-06 09:11:26'),
(2, 'bedrift', 'Green Solutions AS', '923456789', 'Parkveien 5, Bergen', '2026-03-06 09:11:26'),
(3, 'privat', NULL, NULL, 'Bjørnsons gate 8, Trondheim', '2026-03-06 09:11:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  ADD PRIMARY KEY (`kontakt_id`),
  ADD KEY `kunde_id` (`kunde_id`);

--
-- Indexes for table `kunder`
--
ALTER TABLE `kunder`
  ADD PRIMARY KEY (`kunde_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kunder`
--
ALTER TABLE `kunder`
  MODIFY `kunde_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  ADD CONSTRAINT `kontaktpersoner_ibfk_1` FOREIGN KEY (`kunde_id`) REFERENCES `kunder` (`kunde_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
