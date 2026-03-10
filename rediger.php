<?php
require_once "db.php";

// ========================
// Rediger KUNDE
// ========================
if (isset($_POST['EditKunde'])) {
    $editing_kunde_id = intval($_POST['EditKunde']);
    header("Location: index.php?edit_kunde=" . $editing_kunde_id);
    exit();
}

if (isset($_POST['SaveKunde'])) {
    $kunde_id = intval($_POST['SaveKunde']);
    $kundetype = $conn->real_escape_string($_POST['kundetype']);
    $firmanavn = $conn->real_escape_string($_POST['firmanavn']);
    $organisasjonsnummer = $conn->real_escape_string($_POST['organisasjonsnummer']);
    $adresse = $conn->real_escape_string($_POST['adresse']);
    $opprettet_dato = date('Y-m-d H:i:s');

    $sql = "UPDATE kunder SET 
                kundetype = '$kundetype', 
                firmanavn = '$firmanavn', 
                organisasjonsnummer = '$organisasjonsnummer', 
                adresse = '$adresse', 
                opprettet_dato = '$opprettet_dato' 
            WHERE kunde_id = $kunde_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=kunde_oppdatert");
    } else {
        header("Location: index.php?error=" . urlencode($conn->error));
    }
    exit();
}

// ========================
// Rediger KONTAKTPERSON
// ========================
if (isset($_POST['EditKontakt'])) {
    $editing_kontakt_id = intval($_POST['EditKontakt']);
    header("Location: index.php?edit_kontakt=" . $editing_kontakt_id);
    exit();
}

if (isset($_POST['SaveKontakt'])) {
    $kontakt_id = intval($_POST['SaveKontakt']);
    $kunde_id = intval($_POST['kunde_id']);
    $fornavn = $conn->real_escape_string($_POST['fornavn']);
    $etternavn = $conn->real_escape_string($_POST['etternavn']);
    $epost = $conn->real_escape_string($_POST['epost']);
    $stilling = $conn->real_escape_string($_POST['stilling']);
    $opprettet_dato = date('Y-m-d H:i:s');

    $sql = "UPDATE kontaktpersoner SET 
                kunde_id = $kunde_id,
                fornavn = '$fornavn', 
                etternavn = '$etternavn', 
                epost = '$epost', 
                stilling = '$stilling',
                opprettet_dato = '$opprettet_dato' 
            WHERE kontakt_id = $kontakt_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=kontakt_oppdatert");
    } else {
        header("Location: index.php?error=" . urlencode($conn->error));
    }
    exit();
}

// Hvis ingenting ble sendt — gå tilbake
header("Location: index.php");
exit();
?>