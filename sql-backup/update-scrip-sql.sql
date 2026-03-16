-- --------------------------------------------------------
-- CRM Database - oppdatert struktur med ansatte og normalisert adresse
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Tabell for ansatte
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS ansatte (
    ansatt_id INT(11) NOT NULL AUTO_INCREMENT,
    brukernavn VARCHAR(50) NOT NULL UNIQUE,
    passord_hash VARCHAR(255) NOT NULL,
    fornavn VARCHAR(100) NOT NULL,
    etternavn VARCHAR(100) NOT NULL,
    epost VARCHAR(100) DEFAULT NULL,
    rolle ENUM('admin','selger','support') DEFAULT 'selger',
    opprettet_dato TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ansatt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabell for steder
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS steder (
    sted_id INT(11) NOT NULL AUTO_INCREMENT,
    poststed VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (sted_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabell for postnumre
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS postnumre (
    postnummer VARCHAR(10) NOT NULL,
    sted_id INT(11) NOT NULL,
    PRIMARY KEY (postnummer),
    CONSTRAINT fk_postnummer_sted
        FOREIGN KEY (sted_id)
        REFERENCES steder(sted_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabell for adresser
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS adresser (
    adresse_id INT(11) NOT NULL AUTO_INCREMENT,
    gate VARCHAR(255) NOT NULL,
    postnummer VARCHAR(10) NOT NULL,
    PRIMARY KEY (adresse_id),
    CONSTRAINT fk_adresse_postnummer
        FOREIGN KEY (postnummer)
        REFERENCES postnumre(postnummer)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabell for kunder
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS kunder (
    kunde_id INT(11) NOT NULL AUTO_INCREMENT,
    kundetype ENUM('privat','bedrift') NOT NULL,
    firmanavn VARCHAR(100) DEFAULT NULL,
    organisasjonsnummer VARCHAR(50) DEFAULT NULL UNIQUE,
    adresse_id INT(11) DEFAULT NULL,
    opprettet_dato TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (kunde_id),
    CONSTRAINT fk_kunder_adresse
        FOREIGN KEY (adresse_id)
        REFERENCES adresser(adresse_id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabell for kontaktpersoner
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS kontaktpersoner (
    kontakt_id INT(11) NOT NULL AUTO_INCREMENT,
    kunde_id INT(11) NOT NULL,
    fornavn VARCHAR(100) NOT NULL,
    etternavn VARCHAR(100) NOT NULL,
    epost VARCHAR(100) DEFAULT NULL,
    telefon VARCHAR(30) DEFAULT NULL,
    stilling VARCHAR(100) DEFAULT NULL,
    opprettet_dato TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (kontakt_id),
    KEY kunde_id (kunde_id),
    CONSTRAINT kontaktpersoner_ibfk_1
        FOREIGN KEY (kunde_id)
        REFERENCES kunder(kunde_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;