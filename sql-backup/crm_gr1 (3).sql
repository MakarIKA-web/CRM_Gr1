-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 17. Mar, 2026 12:19 PM
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
-- Tabellstruktur for tabell `adresser`
--

CREATE TABLE `adresser` (
  `adresse_id` int(11) NOT NULL,
  `gate` varchar(255) NOT NULL,
  `postnummer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `ansatte`
--

CREATE TABLE `ansatte` (
  `ansatt_id` int(11) NOT NULL,
  `brukernavn` varchar(50) NOT NULL,
  `passord_hash` varchar(255) NOT NULL,
  `fornavn` varchar(100) NOT NULL,
  `etternavn` varchar(100) NOT NULL,
  `epost` varchar(100) DEFAULT NULL,
  `rolle` enum('admin','selger','support') DEFAULT 'selger',
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(22, 1, 'Lars', 'Hansen5656', 'lars@nordtech.no', '91234567', 'Daglig leder', '2026-03-12 10:12:15'),
(23, 1, 'Maria', 'Olsen', 'maria@nordtech.no', '92345678', 'Salgssjef', '2026-03-12 10:12:15');

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
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp(),
  `postnummer` varchar(10) DEFAULT NULL,
  `poststed` varchar(100) DEFAULT NULL
) ;

--
-- Dataark for tabell `kunder`
--

INSERT INTO `kunder` (`kunde_id`, `kundetype`, `firmanavn`, `organisasjonsnummer`, `adresse`, `opprettet_dato`, `postnummer`, `poststed`) VALUES
(1, 'bedrift', 'NordTech AS', '912345678', 'Storgata 12', '2026-03-06 09:11:26', '0155', 'Oslo'),
(3, 'privat', NULL, NULL, 'Bjørnsons gate 8', '2026-03-06 09:11:26', '7011', 'Trondheim'),
(4, 'bedrift', 'Rema 1000', '566456765', 'Storgata 12', '2026-03-10 12:09:45', NULL, 'Oslo');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `postnumre`
--

CREATE TABLE `postnumre` (
  `postnummer` varchar(10) NOT NULL,
  `sted_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `steder`
--

CREATE TABLE `steder` (
  `sted_id` int(11) NOT NULL,
  `poststed` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adresser`
--
ALTER TABLE `adresser`
  ADD PRIMARY KEY (`adresse_id`),
  ADD KEY `fk_adresse_postnummer` (`postnummer`);

--
-- Indexes for table `ansatte`
--
ALTER TABLE `ansatte`
  ADD PRIMARY KEY (`ansatt_id`),
  ADD UNIQUE KEY `brukernavn` (`brukernavn`);

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
  ADD PRIMARY KEY (`kunde_id`),
  ADD UNIQUE KEY `unik_organisasjonsnummer` (`organisasjonsnummer`);

--
-- Indexes for table `postnumre`
--
ALTER TABLE `postnumre`
  ADD PRIMARY KEY (`postnummer`),
  ADD KEY `fk_postnummer_sted` (`sted_id`);

--
-- Indexes for table `steder`
--
ALTER TABLE `steder`
  ADD PRIMARY KEY (`sted_id`),
  ADD UNIQUE KEY `poststed` (`poststed`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adresser`
--
ALTER TABLE `adresser`
  MODIFY `adresse_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ansatte`
--
ALTER TABLE `ansatte`
  MODIFY `ansatt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `kunder`
--
ALTER TABLE `kunder`
  MODIFY `kunde_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `steder`
--
ALTER TABLE `steder`
  MODIFY `sted_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `adresser`
--
ALTER TABLE `adresser`
  ADD CONSTRAINT `fk_adresse_postnummer` FOREIGN KEY (`postnummer`) REFERENCES `postnumre` (`postnummer`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  ADD CONSTRAINT `kontaktpersoner_ibfk_1` FOREIGN KEY (`kunde_id`) REFERENCES `kunder` (`kunde_id`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `postnumre`
--
ALTER TABLE `postnumre`
  ADD CONSTRAINT `fk_postnummer_sted` FOREIGN KEY (`sted_id`) REFERENCES `steder` (`sted_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
