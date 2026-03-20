<?php
session_start();
require_once "config.php";

// Sjekk at bare admin kan registrere nye ansatte
if (!isset($_SESSION['ansatt_id']) || $_SESSION['rolle'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brukernavn = trim($_POST['brukernavn']);
    $fornavn = trim($_POST['fornavn']);
    $etternavn = trim($_POST['etternavn']);
    $epost = trim($_POST['epost']);
    $rolle = $_POST['rolle'];
    $passord = $_POST['passord'];

    // Enkel validering
    if (empty($brukernavn) || empty($fornavn) || empty($etternavn) || empty($passord)) {
        $error = "Fyll inn alle obligatoriske felt!";
    } else {
        // Hash passord
        $passord_hash = password_hash($passord, PASSWORD_DEFAULT);

        // Sjekk om brukernavn allerede finnes
        $stmt = $conn->prepare("SELECT ansatt_id FROM ansatte WHERE brukernavn=? OR epost=?");
        $stmt->bind_param("ss", $brukernavn, $epost);
        if (!$stmt->execute()) { // utfører spørringen 
            return "SQL-feil: " . $stmt->error;
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Brukernavn eller epost finnes allerede!";
        } else {
            // Sett inn i databasen
            $stmt = $conn->prepare("INSERT INTO ansatte (brukernavn, passord_hash, fornavn, etternavn, epost, rolle) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $brukernavn, $passord_hash, $fornavn, $etternavn, $epost, $rolle);

            if ($stmt->execute()) {
                $success = "Ny ansatt registrert!";
            } else {
                $error = "Feil ved registrering: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<title>Registrer ny ansatt</title>
</head>
<body>
<h2>Registrer ny ansatt</h2>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <label>Brukernavn: <input type="text" name="brukernavn" required></label><br><br>
    <label>Fornavn: <input type="text" name="fornavn" required></label><br><br>
    <label>Etternavn: <input type="text" name="etternavn" required></label><br><br>
    <label>E-post: <input type="email" name="epost"></label><br><br>
    <label>Rolle:
        <select name="rolle" required>
            <option value="selger">Selger</option>
            <option value="support">Support</option>
            <option value="admin">Admin</option>
        </select>
    </label><br><br>
    <label>Passord: <input type="password" name="passord" required></label><br><br>
    <button type="submit">Registrer ansatt</button>
</form>

<p><a href="index.php">Tilbake til dashboard</a></p>
</body>
</html>