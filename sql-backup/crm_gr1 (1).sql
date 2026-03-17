-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 01:02 PM
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
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kunder`
--

INSERT INTO `kunder` (`kunde_id`, `kundetype`, `firmanavn`, `organisasjonsnummer`, `adresse_id`, `opprettet_dato`) VALUES
(1, 'bedrift', 'NordTech AS', '912345678', 1, '2026-03-17 11:55:13');

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
  ADD UNIQUE KEY `organisasjonsnummer` (`organisasjonsnummer`),
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
  MODIFY `adresse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ansatte`
--
ALTER TABLE `ansatte`
  MODIFY `ansatt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontaktpersoner`
--
ALTER TABLE `kontaktpersoner`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kunder`
--
ALTER TABLE `kunder`
  MODIFY `kunde_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `kontaktpersoner_ibfk_1` FOREIGN KEY (`kunde_id`) REFERENCES `kunder` (`kunde_id`) ON DELETE CASCADE;

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
