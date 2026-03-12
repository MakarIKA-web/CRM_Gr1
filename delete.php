<?php
require_once "config.php";

// Slett kunde
if (isset($_GET['id'])) {
    $kunde_id = intval($_GET['id']);

    // Slett kontaktpersoner knyttet til kunden først
    $sql = "DELETE FROM kontaktpersoner WHERE kunde_id = $kunde_id";
    $conn->query($sql);

    // Slett kunden
    $sql = "DELETE FROM kunder WHERE kunde_id = $kunde_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=kunde_slettet");
    } else {
        header("Location: index.php?error=" . urlencode($conn->error));
    }
    exit();
}

// Slett kontaktperson
if (isset($_GET['kontakt_id'])) {
    $kontakt_id = intval($_GET['kontakt_id']);

    $sql = "DELETE FROM kontaktpersoner WHERE kontakt_id = $kontakt_id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=kontakt_slettet");
    } else {
        header("Location: index.php?error=" . urlencode($conn->error));
    }
    exit();
}

// Hvis ingenting ble sendt — gå tilbake
header("Location: index.php");
exit();
?>