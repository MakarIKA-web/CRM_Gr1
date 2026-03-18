<?php
// requests.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "db.php";
require_once "functions.php";

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

    // Legg til ny kontaktperson
    if (isset($_POST["InsertKontakt"])) {

        $kunde_id = intval($_POST['kunde_id']);
        $fornavn = $conn->real_escape_string($_POST['fornavn']);
        $etternavn = $conn->real_escape_string($_POST['etternavn']);
        $epost = $conn->real_escape_string($_POST['epost']);
        $telefon = $conn->real_escape_string($_POST['telefon']);
        $stilling = $conn->real_escape_string($_POST['stilling']);
        $opprettet_dato = date('Y-m-d H:i:s');

        $sql = "INSERT INTO kontaktpersoner (kunde_id, fornavn, etternavn, epost, telefon, stilling, opprettet_dato)
                VALUES ($kunde_id, '$fornavn', '$etternavn', '$epost', '$telefon', '$stilling', '$opprettet_dato')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Ny kontaktperson lagt til!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Feil: " . addslashes($conn->error) . "'); window.location='addkontakt.php';</script>";
        }

    }

}
?>