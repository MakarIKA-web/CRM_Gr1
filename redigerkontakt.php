<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['ansatt_id'])) {
    header("Location: login.php");
    exit;
}

// Hent alle kunder for dropdown
$kundeliste = []; // liste for å holde kunder
$sqlAllKunder = "SELECT kunde_id, firmanavn FROM kunder";
$resultAllKunder = $conn->query($sqlAllKunder); // putter resultatet i en variabel

// Legger alle kunder i en array
while ($k = $resultAllKunder->fetch_assoc()) {
    $kundeliste[] = $k;
}

// Sjekk om kontaktperson-ID er satt
if (!isset($_GET['id'])) {
    die("Ingen kontaktperson valgt.");
}

// Hent kontaktperson-ID fra URL og sørg for at det er et tall
$kontakt_id = intval($_GET['id']);

// Hent kontaktpersoninformasjon
$sqlKontakt = "SELECT kp.*, k.firmanavn
               FROM kontaktpersoner kp
               JOIN kunder k ON kp.kunde_id = k.kunde_id
               WHERE kp.kontakt_id = $kontakt_id";
$resultKontakt = $conn->query($sqlKontakt); // putter resultatet i en variabel

if ($resultKontakt->num_rows == 0) { // Hvis ingen kontaktperson finnes med denne ID-en
    die("Kontaktperson ikke funnet."); // stopper skriptet og viser en melding
}

$kontakt = $resultKontakt->fetch_assoc(); // henter kontakt

// Behandle oppdatering når skjema sendes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn = $conn->real_escape_string($_POST['fornavn']); // tar fornavn fra skjemaet og gjør det trygt for SQL
    $en = $conn->real_escape_string($_POST['etternavn']); // tar etternavn fra skjemaet og gjør det trygt for SQL
    $ep = $conn->real_escape_string($_POST['epost']); // tar epost fra skjemaet og gjør det trygt for SQL
    $tel = $conn->real_escape_string($_POST['telefon']); // tar telefon fra skjemaet og gjør det trygt for SQL
    $st = $conn->real_escape_string($_POST['stilling']); // tar stilling fra skjemaet og gjør det trygt for SQL

    // Oppdater kontaktperson i databasen
    $sqlUpdate = "UPDATE kontaktpersoner SET
                    fornavn='$fn',
                    etternavn='$en',
                    epost='$ep',
                    telefon='$tel',
                    stilling='$st'
                  WHERE kontakt_id=$kontakt_id";

    // Hvis oppdateringen er vellykket, gå tilbake til oversikten
    if ($conn->query($sqlUpdate)) {
        // Etter oppdatering
        header("Location: index.php"); // går tilbake til oversikten
        exit;
    // Hvis det oppstår en feil, vis en feilmelding
    } else {
        $error = "Kunne ikke oppdatere kontaktperson: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rediger Kontaktperson</title>
    <link rel="stylesheet" href="src/css/styl.css">
</head>
<body class="form-page">
<main>
    
    <section class="form-hero">
        <h1>Rediger kontaktperson for <?php echo htmlspecialchars($kontakt['firmanavn']); ?></h1>
        <p>Oppdater informasjonen for denne kontaktpersonen</p>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </section>

    <div class="form-card">
        <form method="post">

            <div class="field-group">
                <label for="kunde_id">Firmanavn</label>
                <select name="kunde_id" id="kunde_id" required>
                    <!-- her legger vi til alternativene for kundene -->
                    <option value="">Velg firma</option> <!-- en tom option for å tvinge brukeren til å velge -->

                    <!-- her er en funskjon som henter kunder fra kundearray og legger dem til i dropdownen -->
                    <?php foreach ($kundeliste as $kunde): ?>
                        <!-- hver kunde får et opton som inneholder firmanavn og kunde_id og skal kunne velges av brukeren -->
                        <option value="<?php echo $kunde['kunde_id']; ?>">
                            <?php echo htmlspecialchars($kunde['firmanavn']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-group">
                <label for="fornavn">Fornavn</label>
                <input type="text" name="fornavn" id="fornavn" value="<?php echo htmlspecialchars($kontakt['fornavn']); ?>" required>
            </div>

            <div class="field-group">
                <label for="etternavn">Etternavn</label>
                <input type="text" name="etternavn" id="etternavn" value="<?php echo htmlspecialchars($kontakt['etternavn']); ?>" required>
            </div>

            <div class="field-group">
                <label for="epost">E-post</label>
                <input type="email" name="epost" id="epost" value="<?php echo htmlspecialchars($kontakt['epost']); ?>" required>
            </div>

            <div class="field-group">
                <label for="telefon">Telefon</label>
                <input type="text" name="telefon" id="telefon" value="<?php echo htmlspecialchars($kontakt['telefon']); ?>" pattern="[0-9]{8}" maxlength="8" minlength="8" inputmode="numeric" required>
            </div>

            <div class="field-group">
                <label for="stilling">Stilling</label>
                <input type="text" name="stilling" id="stilling" value="<?php echo htmlspecialchars($kontakt['stilling']); ?>" required>
            </div>

            <hr class="field-divider">

            <button type="submit" class="submit-btn">Oppdater kontaktperson</button>
        </form>
    </div>

    <a href="index.php" class="back-link">Tilbake til oversikten</a>
</main>
</body>
</html>