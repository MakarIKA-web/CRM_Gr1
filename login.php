<?php
session_start();
require_once "config.php";

// Sjekk om bruker er logget inn
if (isset($_SESSION['ansatt_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brukernavn = trim($_POST['brukernavn']);
    $passord = $_POST['passord'];

    // Hent bruker fra databasen
    $stmt = $conn->prepare("SELECT ansatt_id, brukernavn, passord_hash, rolle FROM ansatte WHERE brukernavn=? LIMIT 1");
    $stmt->bind_param("s", $brukernavn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $ansatt = $result->fetch_assoc();
        if (password_verify($passord, $ansatt['passord_hash'])) {
            // Logg inn brukeren
            $_SESSION['ansatt_id'] = $ansatt['ansatt_id'];
            $_SESSION['brukernavn'] = $ansatt['brukernavn'];
            $_SESSION['rolle'] = $ansatt['rolle'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Feil passord!";
        }
    } else {
        $error = "Brukernavn finnes ikke!";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<title>Logg inn</title>
</head>
<body>
<h2>Logg inn</h2>
<form method="post">
    <input type="text" name="brukernavn" placeholder="Brukernavn" required>
    <input type="password" name="passord" placeholder="Passord" required>
    <button type="submit">Logg inn</button>
</form>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>