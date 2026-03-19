<?php
session_start();
require_once "config.php";

// Sjekk om bruker er logget inn
if (isset($_SESSION['ansatt_id'])) {
    header("Location: index.php");
    exit;
}

// Håndter innloggingsforsøk
$error = '';

// når skjemaet sendes, henter vi brukernavn og passord, og sjekker mot databasen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // henter og saniterer input, trim brukes for å fjerne unødvendige mellomrom
    $brukernavn = trim($_POST['brukernavn']);
    $passord = $_POST['passord'];

    // Hent bruker fra databasen
    $stmt = $conn->prepare("SELECT ansatt_id, brukernavn, passord_hash, rolle FROM ansatte WHERE brukernavn=? LIMIT 1"); // limiterer til 1 for sikkerhet
    $stmt->bind_param("s", $brukernavn); // binder parameter for å forhindre SQL-injeksjon
    $stmt->execute(); // utfører spørringen
    $result = $stmt->get_result(); // henter resultatet

    // resultatet skal være 1 rad hvis brukernavn finnes, og vi sjekker passordet med password_verify
    if ($result->num_rows === 1) {
        $ansatt = $result->fetch_assoc(); // henter raden som en assosiativ array
        if (password_verify($passord, $ansatt['passord_hash'])) { // sjekker passordet mot hash i databasen
            // Logg inn brukeren
            $_SESSION['ansatt_id'] = $ansatt['ansatt_id']; // lagrer ansatt_id i session for å holde brukeren logget inn
            $_SESSION['brukernavn'] = $ansatt['brukernavn']; // lagrer brukernavn i session for enkel tilgang
            $_SESSION['rolle'] = $ansatt['rolle']; // lagrer rolle i session for tilgangskontroll

            header("Location: index.php"); // sender brukeren til index.php etter vellykket innlogging
            exit; // stopper videre utførelse av scriptet
        } else { // hvis passordet ikke stemmer, vis en feilmelding
            $error = "Feil passord!";
        }
    } else { // hvis brukernavn ikke finnes, vis en feilmelding
        $error = "Brukernavn finnes ikke!";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../CRM_Gr1/src/css/styll.css">
<title>Logg inn</title>
</head>
<body>

<button class="theme-toggle" onclick="toggleTheme()">Tema</button>

<div class="form-card">
    <h2>Logg inn</h2>
    <form method="post">
        <div class="field-group">
            <label>Brukernavn</label>
            <input type="text" name="brukernavn" placeholder="Brukernavn" required>
        </div>
        <div class="field-group">
            <label>Passord</label>
            <input type="password" name="passord" placeholder="Passord" required>
        </div>
       <button type="submit" class="submit-btn">Logg inn</button>
    </form>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
</div>

<script>
    if (localStorage.getItem('tema') === 'light') document.body.classList.add('light');
    function toggleTheme() {
        document.body.classList.toggle('light');
        localStorage.setItem('tema', document.body.classList.contains('light') ? 'light' : 'dark');
    }
</script>
</body>
</html>