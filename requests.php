<?php
// requests.php
require_once "config.php";      // For $conn

// Handle delete / edit / save / add requests
$editing_id = null; // will be used when rendering the row as editable

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Delete
    // vi bruker en GET-forespørsel for å slette, så vi må sende med ID-en til raden vi vil slette
    if (isset($_GET["DeleteFunction"])) { // isset brukes for å sjekke om variabelen er eksistert
        $id = htmlspecialchars($_GET["DeleteFunction"], ENT_QUOTES, 'UTF-8');
        SQLDEL($id);
    }

    // Cancel delete
    if (isset($_POST['CancelDeleteFunction'])) {
        header("Location: index.php");
        exit();
    }

    // Confirm delete
    if (isset($_POST['ConfirmDeleteFunction'])) {
        $id = htmlspecialchars($_POST['id'], ENT_QUOTES, 'UTF-8');

        // Correct table name
        $sql = "DELETE FROM kjaeledyr WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: index.php");
        exit();
    }

    // Start editing - set $editing_id so the row renders as inputs
    if (isset($_POST["EditFunction"])) { // isset brukes for å sjekke om variabelen er eksistert
        $editing_id = htmlspecialchars($_POST["EditFunction"], ENT_QUOTES, 'UTF-8');
        // do NOT refresh here so the page can render the edit inputs
    }

    // Save updated row
    if (isset($_POST["SaveFunction"])) { // isset brukes for å sjekke om variabelen er eksistert
        $id = htmlspecialchars($_POST["SaveFunction"]); // vi tar ID-en fra knappen som ble trykket
        $navn = isset($_POST["navn"]) ? htmlspecialchars($_POST["navn"], ENT_QUOTES, 'UTF-8') : ""; // henter navn
        $type = isset($_POST["type"]) ? htmlspecialchars($_POST["type"], ENT_QUOTES, 'UTF-8') : ""; // henter type
        $rase = isset($_POST["rase"]) ? htmlspecialchars($_POST["rase"], ENT_QUOTES, 'UTF-8') : ""; // henter rase
        $foedselsdato = isset($_POST["foedselsdato"]) ? htmlspecialchars($_POST["foedselsdato"], ENT_QUOTES, 'UTF-8') : ""; // henter fødselsdato
        $eier_navn = isset($_POST["eier_navn"]) ? htmlspecialchars($_POST["eier_navn"], ENT_QUOTES, 'UTF-8') : ""; // henter eier navn
        $telefon = isset($_POST["telefon"]) ? htmlspecialchars($_POST["telefon"], ENT_QUOTES, 'UTF-8') : ""; // henter telefon
        $notater = isset($_POST["notater"]) ? htmlspecialchars($_POST["notater"], ENT_QUOTES, 'UTF-8') : ""; // henter notater
        SQLUPDATE($id, $navn, $type, $rase, $foedselsdato, $eier_navn, $telefon, $notater); // bruker SQLUPDATE-funksjonen med parametrene
    }

    // Cancel editing (simple refresh)
    if (isset($_POST["CancelEdit"])) {
        header("Refresh:0");
    }

    // Insert new student
    if (isset($_POST["InsertFunction"])) {
        $result = leggTilKundeMedKontaktpersoner($_POST, $conn);

        if ($result === true) {
            echo "<script>alert('Ny kunde og kontaktperson(er) lagt til!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Feil: " . addslashes($result) . "');</script>";
        }
    }
}
?>