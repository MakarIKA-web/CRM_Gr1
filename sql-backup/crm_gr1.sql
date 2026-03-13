-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `kunder` (
  `kunde_id` int(11) NOT NULL,
  `kundetype` enum('privat','bedrift') NOT NULL,
  `firmanavn` varchar(100) DEFAULT NULL,
  `organisasjonsnummer` varchar(50) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `postnummer` varchar(10) DEFAULT NULL,
  `poststed` varchar(100) DEFAULT NULL,
  `opprettet_dato` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kunder`
(`kunde_id`, `kundetype`, `firmanavn`, `organisasjonsnummer`, `adresse`, `postnummer`, `poststed`, `opprettet_dato`)
VALUES
(1, 'bedrift', 'NordTech AS', '912345678', 'Storgata 12', '0155', 'Oslo', '2026-03-06 09:11:26'),
(2, 'bedrift', 'Green Solutions AS', '923456789', 'Parkveien 5', '5007', 'Bergen', '2026-03-06 09:11:26'),
(3, 'privat', NULL, NULL, 'Bjørnsons gate 8', '7011', 'Trondheim', '2026-03-06 09:11:26');


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

INSERT INTO `kontaktpersoner`
(`kontakt_id`, `kunde_id`, `fornavn`, `etternavn`, `epost`, `telefon`, `stilling`, `opprettet_dato`)
VALUES
(1, 1, 'Lars', 'Hansen', 'lars@nordtech.no', '91234567', 'Daglig leder', '2026-03-06 09:11:26'),
(2, 1, 'Maria', 'Olsen', 'maria@nordtech.no', '92345678', 'Salgssjef', '2026-03-06 09:11:26'),
(3, 2, 'Anders', 'Johansen', 'anders@greensolutions.no', '93456789', 'Prosjektleder', '2026-03-06 09:11:26'),
(4, 2, 'Sofie', 'Nilsen', 'sofie@greensolutions.no', '94567890', 'Økonomisjef', '2026-03-06 09:11:26'),
(5, 3, 'Ola', 'Nordmann', 'ola.nordmann@email.no', '95678901', NULL, '2026-03-06 09:11:26');


ALTER TABLE `kunder`
  ADD PRIMARY KEY (`kunde_id`);

ALTER TABLE `kontaktpersoner`
  ADD PRIMARY KEY (`kontakt_id`),
  ADD KEY `kunde_id` (`kunde_id`);

ALTER TABLE `kunder`
  MODIFY `kunde_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `kontaktpersoner`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `kontaktpersoner`
  ADD CONSTRAINT `kontaktpersoner_ibfk_1`
  FOREIGN KEY (`kunde_id`)
  REFERENCES `kunder` (`kunde_id`)
  ON DELETE CASCADE;

COMMIT;