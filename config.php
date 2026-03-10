<?php
require_once "db.php";
$editing_kunde_id = isset($_GET['edit_kunde']) ? intval($_GET['edit_kunde']) : null;
$editing_kontakt_id = isset($_GET['edit_kontakt']) ? intval($_GET['edit_kontakt']) : null;
?>