<?php
require_once "config.php";

session_start();
if (!isset($_SESSION['ansatt_id'])) {
    header("Location: login.php");
    exit;
}

// Sjekk om kunde-ID er satt
if (!isset($_GET['id'])) {
    die("Ingen kunde valgt.");
}

$kunde_id = intval($_GET['id']);

// Hent kunde med adresse, postnummer og poststed
$sqlKunde = "
SELECT k.kunde_id, k.kundetype, k.firmanavn, k.organisasjonsnummer,
       a.gate AS adresse, p.postnummer, s.poststed, k.adresse_id
FROM kunder k
LEFT JOIN adresser a ON k.adresse_id = a.adresse_id
LEFT JOIN postnumre p ON a.postnummer = p.postnummer
LEFT JOIN steder s ON p.sted_id = s.sted_id
WHERE k.kunde_id = $kunde_id
";
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
    $firmanavn = $_POST['firmanavn'];
    $kundetype = $_POST['kundetype'];
    $orgnr     = $_POST['organisasjonsnummer'];
    $gate      = $_POST['adresse'];
    $postnummer= $_POST['postnummer'];
    $poststed  = $_POST['poststed'];

    
    $stmt = $conn->prepare("SELECT sted_id FROM steder WHERE poststed = ?");
    $stmt->bind_param("s", $poststed);
    $stmt->execute(); // utfører spørringen
    $stmt->store_result();
    $stmt->bind_result($sted_id);

    if ($stmt->num_rows > 0) {
        $stmt->fetch(); // hent sted_id fra eksisterende rad
    } else {
        $stmtInsert = $conn->prepare("INSERT INTO steder (poststed) VALUES (?)");
        $stmtInsert->bind_param("s", $poststed);
        $stmtInsert->execute();
        $sted_id = $stmtInsert->insert_id; 
        $stmtInsert->close();
    }
    $stmt->close();

    
    $stmt = $conn->prepare("SELECT postnummer FROM postnumre WHERE postnummer = ?");
    $stmt->bind_param("s", $postnummer);
    $stmt->execute(); // utfører spørringen
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $stmtInsert = $conn->prepare("INSERT INTO postnumre (postnummer, sted_id) VALUES (?, ?)");
        $stmtInsert->bind_param("si", $postnummer, $sted_id);
        $stmtInsert->execute();
        $stmtInsert->close();
    }
    $stmt->close();

 
    $stmt = $conn->prepare("SELECT adresse_id FROM adresser WHERE gate = ? AND postnummer = ?");
    $stmt->bind_param("ss", $gate, $postnummer);
    $stmt->execute(); // utfører spørringen
    $stmt->bind_result($adresse_id);
    if (!$stmt->fetch()) {
        $stmtInsert = $conn->prepare("INSERT INTO adresser (gate, postnummer) VALUES (?, ?)");
        $stmtInsert->bind_param("ss", $gate, $postnummer);
        $stmtInsert->execute();
        $adresse_id = $stmtInsert->insert_id;
        $stmtInsert->close();
    }
    $stmt->close();


    $stmt = $conn->prepare("UPDATE kunder SET firmanavn=?, kundetype=?, organisasjonsnummer=?, adresse_id=? WHERE kunde_id=?");
    $stmt->bind_param("sssii", $firmanavn, $kundetype, $orgnr, $adresse_id, $kunde_id);
    $stmt->execute(); // utfører spørringen
    $stmt->close();


    $kontakt_ids = $_POST['kontakt_id'] ?? [];
    $fornavn = $_POST['kontaktperson_fornavn'] ?? [];
    $etternavn = $_POST['kontaktperson_etternavn'] ?? [];
    $epost = $_POST['kontaktperson_epost'] ?? [];
    $telefon = $_POST['kontaktperson_telefon'] ?? [];
    $stilling = $_POST['kontaktperson_stilling'] ?? [];

    $behold_ids = [];

    for ($i = 0; $i < count($fornavn); $i++) {
        $fn = $conn->real_escape_string($fornavn[$i]);
        $en = $conn->real_escape_string($etternavn[$i]);
        $ep = $conn->real_escape_string($epost[$i]);
        $tel = $conn->real_escape_string($telefon[$i]);
        $st = $conn->real_escape_string($stilling[$i]);

        if (!empty($kontakt_ids[$i])) {
            $kid = intval($kontakt_ids[$i]);
            $conn->query("UPDATE kontaktpersoner SET
                            fornavn='$fn',
                            etternavn='$en',
                            epost='$ep',
                            telefon='$tel',
                            stilling='$st'
                            WHERE kontakt_id=$kid AND kunde_id=$kunde_id");
            $behold_ids[] = $kid;
        } else {
            $conn->query("INSERT INTO kontaktpersoner (kunde_id, fornavn, etternavn, epost, telefon, stilling, opprettet_dato)
                        VALUES ($kunde_id, '$fn', '$en', '$ep', '$tel', '$st', NOW())");
            $behold_ids[] = $conn->insert_id;
        }
    }

    // Slett kontaktpersoner som ikke lenger finnes
    if (!empty($behold_ids)) {
        $behold_str = implode(',', $behold_ids);
        $conn->query("DELETE FROM kontaktpersoner WHERE kunde_id=$kunde_id AND kontakt_id NOT IN ($behold_str)");
    } else {
        $conn->query("DELETE FROM kontaktpersoner WHERE kunde_id=$kunde_id");
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
    <link rel="stylesheet" href="src/css/styl.css">
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

            <div class="field-group">
                <label for="postnummer">Postnummer</label>
                <input type="text" name="postnummer" id="postnummer" value="<?php echo htmlspecialchars($kunde['postnummer']); ?>" required>
            </div>

            <div class="field-group">
                <label for="poststed">Poststed</label>
                <input type="text" name="poststed" id="poststed" value="<?php echo htmlspecialchars($kunde['poststed']); ?>" required>
            </div>

            <hr class="field-divider">

            <!-- Kontaktpersoner -->
            <h2>Kontaktpersoner</h2>
            <div id="kontaktperson-container">
                <?php foreach($kontakter as $kontakt): ?>
                    <div class="kontaktperson">
                        <input type="hidden" name="kontakt_id[]" value="<?php echo isset($kontakt['kontakt_id']) ? $kontakt['kontakt_id'] : ''; ?>">
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

<!-- JS for dynamisk kontaktperson og privat/bedrift-firmanavn -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.querySelector('.add-contact-btn');
    const container = document.getElementById('kontaktperson-container');

    addBtn.addEventListener('click', () => {
        const firstKontakt = container.querySelector('.kontaktperson');
        const newKontakt = firstKontakt.cloneNode(true);
        newKontakt.querySelectorAll('input').forEach(input => {
            if (input.type === 'hidden') input.value = '';
            else input.value = '';
        });
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

    const kundetypeSelect = document.getElementById('kundetype');
    const firmanavnInput = document.getElementById('firmanavn');

    function getFirstKontaktNavn() {
        const firstKontakt = container.querySelector('.kontaktperson');
        if (!firstKontakt) return '';
        const fornavn = firstKontakt.querySelector('input[name="kontaktperson_fornavn[]"]').value.trim();
        const etternavn = firstKontakt.querySelector('input[name="kontaktperson_etternavn[]"]').value.trim();
        return `${fornavn} ${etternavn}`.trim();
    }

    const postnummerInput = document.getElementById('postnummer');
    const poststedInput = document.getElementById('poststed');

    postnummerInput.addEventListener('input', () => {
        const pn = postnummerInput.value.trim();
        if (pn.length === 4) { // typisk norsk postnummer har 4 sifre
            fetch(`get_poststed.php?postnummer=${pn}`)
                .then(response => response.json())
                .then(data => {
                    if (data.poststed) {
                        poststedInput.value = data.poststed;
                    }
                })
                .catch(err => console.error(err));
        }
    });

    function updateFirmanavn() {
        if (kundetypeSelect.value === 'privat') {
            firmanavnInput.value = getFirstKontaktNavn();
            firmanavnInput.readOnly = true;
            document.getElementById('organisasjonsnummer').value = '';
            document.getElementById('organisasjonsnummer').required = false;
        } else {
            firmanavnInput.readOnly = false;
            document.getElementById('organisasjonsnummer').required = true;
        }
    }

    kundetypeSelect.addEventListener('change', updateFirmanavn);
    container.addEventListener('input', (e) => {
        if (e.target.matches('input[name="kontaktperson_fornavn[]"], input[name="kontaktperson_etternavn[]"]')) {
            updateFirmanavn();
        }
    });
    updateFirmanavn();
});
</script>

</body>
</html>