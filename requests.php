<?php
// requests.php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Legg til ny kunde + kontaktpersoner
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