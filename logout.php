<?php
session_start();

// Fjern alle session-variabler
$_SESSION = [];

// Ødelegg session
session_destroy();

// Send bruker tilbake til login
header("Location: login.php");
exit;