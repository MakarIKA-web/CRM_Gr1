<?php
require_once "db.php";
require_once "functions.php";

// her henter vi ut id-ene for kunde og kontaktperson hvis de er satt i URL-en, og konverterer dem til heltall for sikkerhet
$editing_kunde_id = isset($_GET['edit_kunde']) ? intval($_GET['edit_kunde']) : null;
$editing_kontakt_id = isset($_GET['edit_kontakt']) ? intval($_GET['edit_kontakt']) : null;
?>