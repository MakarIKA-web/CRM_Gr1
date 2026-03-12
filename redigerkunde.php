<?php
require_once "config.php";

// Sjekk om kunde-ID er satt
if (!isset($_GET['id'])) {
    die("Ingen kunde valgt.");
}

$kunde_id = intval($_GET['id']);

// Hent kundeinformasjon
$sqlKunde = "SELECT * FROM kunder WHERE kunde_id = $kunde_id";
$resultKunde = $conn->query($sqlKunde);
if ($resultKunde->num_rows == 0) {
    die("Kunde ikke funnet.");
}
$kunde = $resultKunde->fetch_assoc();

// Hent kontaktpersoner til kunden
$sqlKontakter = "SELECT * FROM kontaktpersoner WHERE kunde_id = $kunde_id";
$resultKontakter = $conn->query($sqlKontakter);
$kontakter = $resultKontakter->fetch_all(MYSQLI_ASSOC);

// Behandle oppdatering når skjema sendes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Oppdater kunde
    $firmanavn = $conn->real_escape_string($_POST['firmanavn']);
    $kundetype = $conn->real_escape_string($_POST['kundetype']);
    $orgnr = $conn->real_escape_string($_POST['organisasjonsnummer']);
    $adresse = $conn->real_escape_string($_POST['adresse']);

    $sqlUpdateKunde = "UPDATE kunder SET firmanavn='$firmanavn', kundetype='$kundetype', organisasjonsnummer='$orgnr', adresse='$adresse' WHERE kunde_id=$kunde_id";
    $conn->query($sqlUpdateKunde);

    // Slett eksisterende kontaktpersoner
    $conn->query("DELETE FROM kontaktpersoner WHERE kunde_id=$kunde_id");

    // Legg til nye/oppdaterte kontaktpersoner
    $fornavn = $_POST['kontaktperson_fornavn'] ?? [];
    $etternavn = $_POST['kontaktperson_etternavn'] ?? [];
    $epost = $_POST['kontaktperson_epost'] ?? [];
    $telefon = $_POST['kontaktperson_telefon'] ?? [];
    $stilling = $_POST['kontaktperson_stilling'] ?? [];

    for ($i = 0; $i < count($fornavn); $i++) {
        $fn = $conn->real_escape_string($fornavn[$i]);
        $en = $conn->real_escape_string($etternavn[$i]);
        $ep = $conn->real_escape_string($epost[$i]);
        $tel = $conn->real_escape_string($telefon[$i]);
        $st = $conn->real_escape_string($stilling[$i]);

        $conn->query("INSERT INTO kontaktpersoner (kunde_id, fornavn, etternavn, epost, telefon, stilling, opprettet_dato) 
                      VALUES ($kunde_id, '$fn', '$en', '$ep', '$tel', '$st', NOW())");
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rediger Kunde</title>
</head>
<body class="form-page">
<main>
    <section class="form-hero">
        <h1>Rediger kunde</h1>
        <p>Rediger informasjon om kunden</p>
    </section>

    <div class="form-card">
        <form method="post" id="editForm">

            <!-- Kundeinformasjon -->
            <div class="field-group">
                <label for="firmanavn">Firmanavn</label>
                <input type="text" name="firmanavn" id="firmanavn" value="<?php echo htmlspecialchars($kunde['firmanavn']); ?>" required>
            </div>

            <div class="field-group">
                <label for="kundetype">Kundetype</label>
                <select name="kundetype" id="kundetype" required>
                    <option value="">Velg type</option>
                    <option value="privat" <?php if($kunde['kundetype']=='privat') echo 'selected'; ?>>Privat</option>
                    <option value="bedrift" <?php if($kunde['kundetype']=='bedrift') echo 'selected'; ?>>Bedrift</option>
                </select>
            </div>

            <div class="field-group">
                <label for="organisasjonsnummer">Organisasjonsnummer</label>
                <input type="text" name="organisasjonsnummer" id="organisasjonsnummer" value="<?php echo htmlspecialchars($kunde['organisasjonsnummer']); ?>" required minlength="9" maxlength="9">
            </div>

            <div class="field-group">
                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" value="<?php echo htmlspecialchars($kunde['adresse']); ?>" required>
            </div>

            <hr class="field-divider">

            <!-- Kontaktpersoner -->
            <h2>Kontaktpersoner</h2>
            <div id="kontaktperson-container">
                <?php foreach($kontakter as $kontakt): ?>
                    <div class="kontaktperson">
                        <div class="field-group">
                            <label>Fornavn</label>
                            <input type="text" name="kontaktperson_fornavn[]" value="<?php echo htmlspecialchars($kontakt['fornavn']); ?>" required>
                        </div>
                        <div class="field-group">
                            <label>Etternavn</label>
                            <input type="text" name="kontaktperson_etternavn[]" value="<?php echo htmlspecialchars($kontakt['etternavn']); ?>" required>
                        </div>
                        <div class="field-group">
                            <label>E-post</label>
                            <input type="email" name="kontaktperson_epost[]" value="<?php echo htmlspecialchars($kontakt['epost']); ?>" required>
                        </div>
                        <div class="field-group">
                            <label>Telefon</label>
                            <input type="text" name="kontaktperson_telefon[]" value="<?php echo htmlspecialchars($kontakt['telefon']); ?>" pattern="[0-9]{8}" maxlength="8" minlength="8" inputmode="numeric" required>
                        </div>
                        <div class="field-group">
                            <label>Stilling</label>
                            <input type="text" name="kontaktperson_stilling[]" value="<?php echo htmlspecialchars($kontakt['stilling']); ?>" required>
                        </div>
                        <button type="button" class="remove-contact-btn">Fjern kontaktperson</button>
                        <hr class="field-divider">
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="add-contact-btn">Legg til flere kontaktpersoner</button>
            <hr class="field-divider">

            <button type="submit" class="submit-btn">Oppdater kunde</button>
        </form>
    </div>

    <a href="index.php" class="back-link">Tilbake til oversikten</a>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.querySelector('.add-contact-btn');
    const container = document.getElementById('kontaktperson-container');

    addBtn.addEventListener('click', () => {
        const firstKontakt = container.querySelector('.kontaktperson');
        const newKontakt = firstKontakt.cloneNode(true);
        newKontakt.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newKontakt);
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-contact-btn')) {
            const kontaktDiv = e.target.closest('.kontaktperson');
            if (container.querySelectorAll('.kontaktperson').length > 1) {
                kontaktDiv.remove();
            } else {
                alert("Du må ha minst én kontaktperson.");
            }
        }
    });
});
</script>
</body>
</html>