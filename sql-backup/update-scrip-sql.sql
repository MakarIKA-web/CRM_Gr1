START TRANSACTION;

-- 1. Legg til adresse_id i kunder-tabellen
ALTER TABLE kunder
  ADD COLUMN adresse_id INT(11) DEFAULT NULL;

-- 2. Opprett postnummer og sted hvis ikke eksisterer
INSERT IGNORE INTO steder (sted_id, poststed)
SELECT DISTINCT 1, 'Oslo' WHERE NOT EXISTS (SELECT 1 FROM steder WHERE poststed='Oslo');

INSERT IGNORE INTO postnumre (postnummer, sted_id)
SELECT DISTINCT '0155', 1 WHERE NOT EXISTS (SELECT 1 FROM postnumre WHERE postnummer='0155');

-- 3. Opprett adresser og koble til kunder
INSERT IGNORE INTO adresser (adresse_id, gate, postnummer)
SELECT 1, 'Storgata 12', '0155' 
WHERE NOT EXISTS (SELECT 1 FROM adresser WHERE gate='Storgata 12' AND postnummer='0155');

-- 4. Oppdater kunder med adresse_id
UPDATE kunder k
JOIN adresser a ON a.gate='Storgata 12'
SET k.adresse_id = a.adresse_id
WHERE k.kunde_id=1;

-- 5. Fjern gamle kolonner
ALTER TABLE kunder
  DROP COLUMN adresse,
  DROP COLUMN postnummer;

-- 6. Opprett ny fremmednøkkel med ON DELETE SET NULL
ALTER TABLE kunder
  ADD CONSTRAINT fk_kunder_adresse
  FOREIGN KEY (adresse_id) REFERENCES adresser(adresse_id)
  ON DELETE SET NULL;

COMMIT;