<?php
// functions.php
function leggTilKundeMedKontaktpersoner($data, $conn) {

    $sted_id = null;
    $adresse_id = null;

    // 1️⃣ Sanitér kunde-data
    $firmanavn = $data($data['firmanavn'] ?? '', ENT_QUOTES, 'UTF-8');
    $kundetype = $data($data['kundetype'] ?? '', ENT_QUOTES, 'UTF-8');
    $organisasjonsnummer = $data['organisasjonsnummer'] ?? null;
    $adresse = $data($data['adresse'] ?? '', ENT_QUOTES, 'UTF-8');
    $postnummer = $data($data['postnummer'] ?? '', ENT_QUOTES, 'UTF-8');
    $poststed = $data($data['poststed'] ?? '', ENT_QUOTES, 'UTF-8');

    if ($kundetype === 'privat') {
        $organisasjonsnummer = null;
    }

    // 2️⃣ Sjekk om organisasjonsnummer finnes fra før
    if ($organisasjonsnummer) {
        $sjekk = $conn->prepare("SELECT kunde_id FROM kunder WHERE organisasjonsnummer = ?");
        $sjekk->bind_param("s", $organisasjonsnummer);
        $sjekk->execute();
        $res = $sjekk->get_result();
        if ($res->num_rows > 0) {
            $sjekk->close();
            return "Dette organisasjonsnummeret finnes allerede i systemet.";
        }
        $sjekk->close();
    }

    // 3️⃣ Hent eller legg til poststed
    $stmt = $conn->prepare("SELECT sted_id FROM steder WHERE poststed = ?");
    $stmt->bind_param("s", $poststed);
    if (!$stmt->execute()) { // utfører spørringen
        return "SQL-feil: " . $stmt->error;
    }
    $stmt->store_result();
    $stmt->bind_result($sted_id);
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
    } else {
        $stmtInsert = $conn->prepare("INSERT INTO steder (poststed) VALUES (?)");
        $stmtInsert->bind_param("s", $poststed);
        if (!$stmtInsert->execute()) {
            return "SQL-feil (sted): " . $stmtInsert->error;
        }
        $sted_id = $stmtInsert->insert_id;
        $stmtInsert->close();
    }
    $stmt->close();
    if (!$sted_id) {
        return "Kunne ikke finne eller opprette sted.";
    }

    // 4️⃣ Hent eller legg til postnummer
    $stmt = $conn->prepare("SELECT postnummer FROM postnumre WHERE postnummer = ?");
    $stmt->bind_param("s", $postnummer);
    if (!$stmt->execute()) { // utfører spørringen 
        return "SQL-feil: " . $stmt->error;
    }
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $stmtInsert = $conn->prepare("INSERT INTO postnumre (postnummer, sted_id) VALUES (?, ?)");
        $stmtInsert->bind_param("si", $postnummer, $sted_id);
        if (!$stmtInsert->execute()) {
            return "SQL-feil: " . $stmtInsert->error;
        }
        $stmtInsert->close();
    }
    $stmt->close();

    // 5️⃣ Hent eller legg til adresse
    $stmt = $conn->prepare("SELECT adresse_id FROM adresser WHERE gate = ? AND postnummer = ?");
    $stmt->bind_param("ss", $adresse, $postnummer);
    if (!$stmt->execute()) { // utfører spørringen
        return "SQL-feil: " . $stmt->error;
    }
    $stmt->store_result();
    $stmt->bind_result($adresse_id);
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
    } else {
        $stmtInsert = $conn->prepare("INSERT INTO adresser (gate, postnummer) VALUES (?, ?)");
        $stmtInsert->bind_param("ss", $adresse, $postnummer);
        if (!$stmtInsert->execute()) {
            return "SQL-feil: " . $stmtInsert->error;
        }
        $adresse_id = $stmtInsert->insert_id;
        $stmtInsert->close();
    }
    $stmt->close();
    if (!$adresse_id) {
        return "Kunne ikke finne eller opprette adresse.";
    }

    // 6️⃣ Sett inn kunde med adresse_id
    $stmt = $conn->prepare("INSERT INTO kunder (kundetype, firmanavn, organisasjonsnummer, adresse_id, opprettet_dato) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $kundetype, $firmanavn, $organisasjonsnummer, $adresse_id);
    if (!$stmt->execute()) { // utfører spørringen
        return "SQL-feil: " . $stmt->error;
    }
    $kunde_id = $conn->insert_id;
    $stmt->close();

    // 7️⃣ Sett inn kontaktpersoner
    $fornavnArray   = $data['kontaktperson_fornavn'] ?? [];
    $etternavnArray = $data['kontaktperson_etternavn'] ?? [];
    $epostArray     = $data['kontaktperson_epost'] ?? [];
    $telefonArray   = $data['kontaktperson_telefon'] ?? [];
    $stillingArray  = $data['kontaktperson_stilling'] ?? [];

    for ($i = 0; $i < count($fornavnArray); $i++) {
        $fornavn   = htmlspecialchars($fornavnArray[$i], ENT_QUOTES, 'UTF-8');
        $etternavn = htmlspecialchars($etternavnArray[$i], ENT_QUOTES, 'UTF-8');
        $epost     = htmlspecialchars($epostArray[$i], ENT_QUOTES, 'UTF-8');
        $telefon   = htmlspecialchars($telefonArray[$i], ENT_QUOTES, 'UTF-8');
        $stilling  = htmlspecialchars($stillingArray[$i], ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("INSERT INTO kontaktpersoner (kunde_id, fornavn, etternavn, epost, telefon, stilling) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $kunde_id, $fornavn, $etternavn, $epost, $telefon, $stilling);
        if (!$stmt->execute()) {
            $stmt->close();
            return "Feil ved innsetting av kontaktperson: " . $stmt->error;
        }
        $stmt->close();
    }

    return true;
}
?>