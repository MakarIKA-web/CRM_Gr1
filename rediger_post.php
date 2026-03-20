<?php
session_start();
require_once "config.php";

// Kun admin eller support
if (!isset($_SESSION['ansatt_id']) || !in_array($_SESSION['rolle'], ['admin','support'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $postnummer = $_POST['postnummer'];
    $poststed   = trim($_POST['poststed']);

    if (!empty($poststed)) {
        // Oppdater poststed i steder-tabellen
        // Finn først sted_id via postnumre
        $stmt = $conn->prepare("SELECT sted_id FROM postnumre WHERE postnummer = ?");
        $stmt->bind_param("s", $postnummer);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $sted_id = $row['sted_id'];

            // Oppdater stedet
            $stmt2 = $conn->prepare("UPDATE steder SET poststed = ? WHERE sted_id = ?");
            $stmt2->bind_param("si", $poststed, $sted_id);
            if ($stmt2->execute()) {
                $_SESSION['msg'] = "Poststed oppdatert!";
            } else {
                $_SESSION['msg'] = "Feil ved oppdatering: " . $stmt2->error;
            }
        } else {
            $_SESSION['msg'] = "Fant ikke postnummeret i databasen.";
        }
    } else {
        $_SESSION['msg'] = "Poststed kan ikke være tomt!";
    }
}

// Tilbake til oversikten
header("Location: postnumre.php");
exit;