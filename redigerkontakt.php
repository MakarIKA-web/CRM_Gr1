<?php
require_once "config.php";

// Sjekk om kontaktperson-ID er satt
if (!isset($_GET['id'])) {
    die("Ingen kontaktperson valgt.");
}

$kontakt_id = intval($_GET['id']);

// Hent kontaktpersoninformasjon
$sqlKontakt = "SELECT kp.*, k.firmanavn 
               FROM kontaktpersoner kp
               JOIN kunder k ON kp.kunde_id = k.kunde_id
               WHERE kp.kontakt_id = $kontakt_id";
$resultKontakt = $conn->query($sqlKontakt);

if ($resultKontakt->num_rows == 0) {
    die("Kontaktperson ikke funnet.");
}

$kontakt = $resultKontakt->fetch_assoc();

// Behandle oppdatering når skjema sendes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn = $conn->real_escape_string($_POST['fornavn']);
    $en = $conn->real_escape_string($_POST['etternavn']);
    $ep = $conn->real_escape_string($_POST['epost']);
    $tel = $conn->real_escape_string($_POST['telefon']);
    $st = $conn->real_escape_string($_POST['stilling']);

    $sqlUpdate = "UPDATE kontaktpersoner SET 
                    fornavn='$fn', 
                    etternavn='$en', 
                    epost='$ep', 
                    telefon='$tel', 
                    stilling='$st' 
                  WHERE kontakt_id=$kontakt_id";

    if ($conn->query($sqlUpdate)) {
        // Etter oppdatering
        header("Location: index.php"); // går tilbake til oversikten
        exit;
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
    <link rel="stylesheet" href="src/css/style.css">
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