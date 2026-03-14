-- --------------------------------------------------------
-- CRM Database - oppdatert struktur med ansatte og normalisert adresse
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Tegnkoding
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Tabell for ansatte (brukere som kan logge inn)
-- --------------------------------------------------------
CREATE TABLE `ansatte` (
    `ansatt_id` INT(11) NOT NULL AUTO_INCREMENT,
    `brukernavn` VARCHAR(50) NOT NULL UNIQUE,      -- brukernavn for innlogging
    `passord_hash` VARCHAR(255) NOT NULL,         -- lagret som hash
    `fornavn` VARCHAR(100) NOT NULL,
    `etternavn` VARCHAR(100) NOT NULL,
    `epost` VARCHAR(100) DEFAULT NULL,
    `rolle` ENUM('admin','selger','support') DEFAULT 'selger', -- rolle for tilgangskontroll
    `opprettet_dato` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ansatt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Normaliserte adresse-tabeller
-- --------------------------------------------------------

-- Tabell for steder (byer)
CREATE TABLE `steder` (
    `sted_id` INT(11) NOT NULL AUTO_INCREMENT,
    `poststed` VARCHAR(100) NOT NULL UNIQUE,      -- navn på by
    PRIMARY KEY (`sted_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabell for postnumre
CREATE TABLE `postnumre` (
    `postnummer` VARCHAR(10) NOT NULL,
    `sted_id` INT(11) NOT NULL,                   -- kobling til sted
    PRIMARY KEY (`postnummer`),
    CONSTRAINT `fk_postnummer_sted` FOREIGN KEY (`sted_id`) REFERENCES `steder` (`sted_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabell for adresser
CREATE TABLE `adresser` (
    `adresse_id` INT(11) NOT NULL AUTO_INCREMENT,
    `gate` VARCHAR(255) NOT NULL,                 -- gatenavn og nummer
    `postnummer` VARCHAR(10) NOT NULL,           -- kobling til postnummer
    PRIMARY KEY (`adresse_id`),
    CONSTRAINT `fk_adresse_postnummer` FOREIGN KEY (`postnummer`) REFERENCES `postnumre` (`postnummer`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Oppdatert tabell for kunder med adresse_id
-- --------------------------------------------------------
CREATE TABLE `kunder` (
    `kunde_id` INT(11) NOT NULL AUTO_INCREMENT,
    `kundetype` ENUM('privat','bedrift') NOT NULL,
    `firmanavn` VARCHAR(100) DEFAULT NULL,
    `organisasjonsnummer` VARCHAR(50) DEFAULT NULL UNIQUE,
    `adresse_id` INT(11) DEFAULT NULL,           -- kobling til adresser
    `opprettet_dato` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`kunde_id`),
    CONSTRAINT `fk_kunder_adresse` FOREIGN KEY (`adresse_id`) REFERENCES `adresser` (`adresse_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Oppdatert tabell for kontaktpersoner
-- --------------------------------------------------------
CREATE TABLE `kontaktpersoner` (
    `kontakt_id` INT(11) NOT NULL AUTO_INCREMENT,
    `kunde_id` INT(11) NOT NULL,                 -- kobling til kunde
    `fornavn` VARCHAR(100) NOT NULL,
    `etternavn` VARCHAR(100) NOT NULL,
    `epost` VARCHAR(100) DEFAULT NULL,
    `telefon` VARCHAR(30) DEFAULT NULL,
    `stilling` VARCHAR(100) DEFAULT NULL,
    `opprettet_dato` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`kontakt_id`),
    KEY `kunde_id` (`kunde_id`),
    CONSTRAINT `kontaktpersoner_ibfk_1` FOREIGN KEY (`kunde_id`) REFERENCES `kunder` (`kunde_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
