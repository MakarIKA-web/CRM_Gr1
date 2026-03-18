<?php
require_once "config.php";

if (!isset($_GET['postnummer'])) {
    echo json_encode(['poststed' => '']);
    exit;
}

$postnummer = $conn->real_escape_string($_GET['postnummer']);

$sql = "SELECT s.poststed
        FROM postnumre p
        LEFT JOIN steder s ON p.sted_id = s.sted_id
        WHERE p.postnummer = '$postnummer'
        LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['poststed' => $row['poststed']]);
} else {
    echo json_encode(['poststed' => '']);
}
?>