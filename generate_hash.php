<?php
$passord = 'admin123';
$hash = password_hash($passord, PASSWORD_DEFAULT);
echo $hash;
?>