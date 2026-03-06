<?php

// Database configuration
$hostname = "localhost"; // Database host
$username = "root"; // Database username, root is the default username for MySQL
$password = ""; // Database password
$database = "crm_gr1"; // Database name

// Create connection, where $conn is the connection object which represents the connection to the database
// We using new mysqli and then write the connection parameters
$conn = new mysqli($hostname, $username, $password, $database);

// Check connection
if ($conn->connect_error) { 
    // If the connection failed, display an error message
    die("Kunne ikke koble til databasen: " . $conn->connect_error);
}

// Fetch data from the database
$sqlkunder = "SELECT * FROM kunder"; // SQL query to select all data from the 'kunder' table
// Fetch data from the database
$sqlkontaktpersoner = "SELECT * FROM kontaktpersoner"; // SQL query to select all data from the 'kontaktpersoner' table

$resultkunder = $conn->query($sqlkunder); // $result is the result set returned by the query
$resultkontaktpersoner = $conn->query($sqlkontaktpersoner); // $result is the result set returned by the query
?>