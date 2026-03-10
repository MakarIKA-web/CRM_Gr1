<?php
// functions.php
function leggTilKundeMedKontaktpersoner($data, $conn) {
    // 1. Sanitér kunde-data
    $firmanavn = htmlspecialchars($data['firmanavn'] ?? '', ENT_QUOTES, 'UTF-8');
    $kundetype = htmlspecialchars($data['kundetype'] ?? '', ENT_QUOTES, 'UTF-8');
    $organisasjonsnummer = htmlspecialchars($data['organisasjonsnummer'] ?? '', ENT_QUOTES, 'UTF-8');

    // 2. Sett inn kunden
    $stmt = $conn->prepare("INSERT INTO kunder (kundetype, firmanavn, organisasjonsnummer) VALUES (?, ?, ?)");
    if (!$stmt) return "Feil i SQL-preparering: " . $conn->error;

    $stmt->bind_param("sss", $kundetype, $firmanavn, $organisasjonsnummer);
    if (!$stmt->execute()) {
        $stmt->close();
        return "Feil ved innsetting av kunde: " . $stmt->error;
    }

    $kundeId = $conn->insert_id;
    $stmt->close();

    if (!$kundeId) return "Kunde-ID ikke opprettet.";

    // 3. Hent kontaktperson-arrays
    $fornavnArray   = $data['kontaktperson_fornavn'] ?? [];
    $etternavnArray = $data['kontaktperson_etternavn'] ?? [];
    $epostArray     = $data['kontaktperson_epost'] ?? [];
    $telefonArray   = $data['kontaktperson_telefon'] ?? [];
    $stillingArray  = $data['kontaktperson_stilling'] ?? [];

    // 4. Sett inn alle kontaktpersoner
    for ($i = 0; $i < count($fornavnArray); $i++) {
        $fornavn   = htmlspecialchars($fornavnArray[$i], ENT_QUOTES, 'UTF-8');
        $etternavn = htmlspecialchars($etternavnArray[$i], ENT_QUOTES, 'UTF-8');
        $epost     = htmlspecialchars($epostArray[$i], ENT_QUOTES, 'UTF-8');
        $telefon   = htmlspecialchars($telefonArray[$i], ENT_QUOTES, 'UTF-8');
        $stilling  = htmlspecialchars($stillingArray[$i], ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("INSERT INTO kontaktpersoner (kunde_id, fornavn, etternavn, epost, telefon, stilling) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) return "Feil i SQL-preparering for kontaktperson: " . $conn->error;

        $stmt->bind_param("isssss", $kundeId, $fornavn, $etternavn, $epost, $telefon, $stilling);
        if (!$stmt->execute()) {
            $stmt->close();
            return "Feil ved innsetting av kontaktperson: " . $stmt->error;
        }
        $stmt->close();
    }

    return true; // Alt gikk bra
}
?>