-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 18, 2026 at 09:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
-- Table structure for table `adresser`
--

CREATE TABLE `adresser` (
  `adresse_id` int(11) NOT NULL,
  `gate` varchar(255) NOT NULL,
  `postnummer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adresser`
--

INSERT INTO `adresser` (`adresse_id`, `gate`, `postnummer`) VALUES
(2, 'Storgata 12', '0152'),
(3, 'Storgata 12', '0153'),
(4, 'Storgata 12', '0154'),
(1, 'Storgata 12', '0155');

-- --------------------------------------------------------

--
-- Table structure for table `ansatte`
--

CREATE TABLE `ansatte` (
  `ansatt_id` int(11) NOT NULL,
  `brukernavn` varchar(50) NOT NULL,
  `passord_hash` varchar(255) NOT NULL,
  `fornavn` varchar(100) NOT NULL,
  `etternavn` varchar(100) NOT NULL,
  `epost` varchar(100) DEFAULT NULL,
  `rolle` enum('admin','selger','support') DEFAULT 'selger',
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp(),
  `github_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ansatte`
--

INSERT INTO `ansatte` (`ansatt_id`, `brukernavn`, `passord_hash`, `fornavn`, `etternavn`, `epost`, `rolle`, `opprettet_dato`, `github_id`) VALUES
(1, 'makar', '$2y$10$PRtxFgEw4z0d08/wd/lFxenEoajxEMrr.tyx8OvGaSe6DzQ8S0HVK', 'makar', 'hrydkovets', 'makar@admin.no', 'admin', '2026-03-18 08:49:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kontaktpersoner`
--

CREATE TABLE `kontaktpersoner` (
  `kontakt_id` int(11) NOT NULL,
  `kunde_id` int(11) NOT NULL,
  `fornavn` varchar(100) NOT NULL,
  `etternavn` varchar(100) NOT NULL,
  `epost` varchar(100) DEFAULT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `stilling` varchar(100) DEFAULT NULL,
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp(),
  `oppdatert_dato` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontaktpersoner`
--

INSERT INTO `kontaktpersoner` (`kontakt_id`, `kunde_id`, `fornavn`, `etternavn`, `epost`, `telefon`, `stilling`, `opprettet_dato`, `oppdatert_dato`) VALUES
(1, 1, 'Sofie', 'Ovik', 'ovik@epost.no', '46876876', 'Vikar', '2026-03-18 07:37:51', NULL),
(3, 5, 'Sofie', 'Ped', 'sofped@epost.no', '46876834', 'Leder', '2026-03-18 07:53:54', NULL),
(4, 1, 'Ola', 'Henrik', 'olahenrik@epost.no', '76578665', 'Leder', '2026-03-18 08:16:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kunder`
--

CREATE TABLE `kunder` (
  `kunde_id` int(11) NOT NULL,
  `kundetype` enum('privat','bedrift') NOT NULL,
  `firmanavn` varchar(100) DEFAULT NULL,
  `organisasjonsnummer` varchar(50) DEFAULT NULL,
  `adresse_id` int(11) DEFAULT NULL,
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp(),
  `oppdatert_dato` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kunder`
--

INSERT INTO `kunder` (`kunde_id`, `kundetype`, `firmanavn`, `organisasjonsnummer`, `adresse_id`, `opprettet_dato`, `oppdatert_dato`) VALUES
(1, 'bedrift', 'NordTech AS', '912345678', 2, '2026-03-18 07:36:39', NULL),
(5, 'privat', 'Sofie Pedd', '', 1, '2026-03-18 07:53:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `postnumre`
--

CREATE TABLE `postnumre` (
  `postnummer` varchar(10) NOT NULL,
  `sted_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postnumre`
--

INSERT INTO `postnumre` (`postnummer`, `sted_id`) VALUES
('0152', 1),
('0153', 1),
('0154', 1),
('0155', 1);

-- --------------------------------------------------------

--
-- Table structure for table `steder`
--

CREATE TABLE `steder` (
  `sted_id` int(11) NOT NULL,
  `poststed` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `steder`
--

INSERT INTO `steder` (`sted_id`, `poststed`) VALUES
(1, 'Oslo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adresser`
--
ALTER TABLE `adresser`
  ADD PRIMARY KEY (`adresse_id`),
  ADD UNIQUE KEY `uk_gate_postnummer` (`gate`,`postnummer`),
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
  ADD UNIQUE KEY `uk_kunde_epost` (`kunde_id`,`epost`);

--
-- Indexes for table `kunder`
--
ALTER TABLE `kunder`
  ADD PRIMARY KEY (`kunde_id`),
  ADD UNIQUE KEY `organisasjonsnummer` (`organisasjonsnummer`),
  ADD UNIQUE KEY `organisasjonsnummer_2` (`organisasjonsnummer`),
  ADD KEY `fk_kunder_adresse` (`adresse_id`);

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
  MODIFY `adresse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ansatte`
--
ALTER TABLE `ansatte`
  MODIFY `ansatt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kunder`
--
ALTER TABLE `kunder`
  MODIFY `kunde_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `steder`
--
ALTER TABLE `steder`
  MODIFY `sted_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adresser`
--
ALTER TABLE `adresser`
  ADD CONSTRAINT `fk_adresse_postnummer` FOREIGN KEY (`postnummer`) REFERENCES `postnumre` (`postnummer`) ON DELETE CASCADE;

--
-- Constraints for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  ADD CONSTRAINT `fk_kontakt_kunde` FOREIGN KEY (`kunde_id`) REFERENCES `kunder` (`kunde_id`) ON DELETE CASCADE;

--
-- Constraints for table `kunder`
--
ALTER TABLE `kunder`
  ADD CONSTRAINT `fk_kunder_adresse` FOREIGN KEY (`adresse_id`) REFERENCES `adresser` (`adresse_id`) ON DELETE SET NULL;

--
-- Constraints for table `postnumre`
--
ALTER TABLE `postnumre`
  ADD CONSTRAINT `fk_postnummer_sted` FOREIGN KEY (`sted_id`) REFERENCES `steder` (`sted_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;