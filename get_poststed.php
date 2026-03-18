<?php
// get_poststed.php
// her henter vi poststed basert på postnummer for å fylle ut poststed-feltet automatisk i addkunde.php og redigerkunde.php
require_once "config.php";

if (!isset($_GET['postnummer'])) {
    echo json_encode(['poststed' => '']);
    exit;
}

// her bruker vi real_escape_string for å unngå SQL-injection, selv om det er lite sannsynlig at noen vil prøve å utnytte dette via en GET-forespørsel
$postnummer = $conn->real_escape_string($_GET['postnummer']);

// Vi gjør en LEFT JOIN for å hente poststedet basert på postnummeret. Hvis postnummeret ikke finnes, vil poststed være null.
$sql = "SELECT s.poststed
        FROM postnumre p
        LEFT JOIN steder s ON p.sted_id = s.sted_id
        WHERE p.postnummer = '$postnummer'
        LIMIT 1"; // LIMIT 1 for å sikre at vi bare får ett resultat, selv om det skulle være duplikater i databasen (som ikke burde skje)

// Vi utfører spørringen og sjekker resultatet
$result = $conn->query($sql);

// Hvis vi finner et resultat, returnerer vi poststedet som JSON. Hvis ikke, returnerer vi en tom poststed.
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['poststed' => $row['poststed']]);
} else {
    echo json_encode(['poststed' => '']);
}
?>