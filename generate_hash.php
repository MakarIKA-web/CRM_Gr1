<?php
// spesielt fil til å generere hash for passord, som skal legges inn i databasen manuelt for admin-brukeren ved første oppsett
$passord = 'admin123';
$hash = password_hash($passord, PASSWORD_DEFAULT);
echo $hash;
?>