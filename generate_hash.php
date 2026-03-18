<?php
// spesielt fil til å generere hash for passord, som skal legges inn i databasen manuelt for admin-brukeren ved første oppsett
$passord = 'admin123'; // passordet du vil hash'e
$hash = password_hash($passord, PASSWORD_DEFAULT); // generer hash
echo $hash; // kopier denne hash-verdien og bruk den i SQL-insert for admin-brukeren i databasen
?>

<?php
// du kjører denne filen én gang for å få hash-verdien, det gjør du ved å kjøre denne filen i terminalen med kommandoen: php generate_hash.php
// så kopierer du den hash-verdien som skrives ut, og limer den inn i SQL-insert-setningen for admin-brukeren i databasen, slik at admin-brukeren får det passordet du har satt i $passord-variabelen
?>