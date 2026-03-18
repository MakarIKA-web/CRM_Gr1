<?php
require_once "config.php";

// Hent alle kunder for dropdown
$kundeliste = [];
$sqlAllKunder = "SELECT kunde_id, firmanavn FROM kunder";
$resultAllKunder = $conn->query($sqlAllKunder);
while ($k = $resultAllKunder->fetch_assoc()) {
    $kundeliste[] = $k;
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/css/styl.css">
    <title>Ny Kontaktperson</title>
</head>
<body class="form-page">

<main>

    <!-- HERO -->
    <section class="form-hero">
        <h1>Ny kontaktperson</h1>
        <p>Fyll inn informasjon om kontaktpersonen</p>
    </section>

    <!-- FORM CARD -->
    <div class="form-card">
        <form method="post" action="requests.php" id="addForm" enctype="multipart/form-data">

            <div class="field-group">
                <label for="kunde_id">Firmanavn</label>
                <select name="kunde_id" id="kunde_id" required>
                    <option value="">Velg firma</option>
                    <?php foreach ($kundeliste as $kunde): ?>
                        <option value="<?php echo $kunde['kunde_id']; ?>">
                            <?php echo htmlspecialchars($kunde['firmanavn']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field-group">
                <label for="fornavn">Fornavn</label>
                <input type="text" name="fornavn" id="fornavn" placeholder="F.eks. Ola" required>
            </div>

            <div class="field-group">
                <label for="etternavn">Etternavn</label>
                <input type="text" name="etternavn" id="etternavn" placeholder="F.eks. Nordmann" required>
            </div>

            <div class="field-group">
                <label for="epost">E-post</label>
                <input type="email" name="epost" id="epost" placeholder="F.eks. ola@nordmann.no" required>
            </div>

            <div class="field-group">
                <label for="telefon">Telefon</label>
                <input
                    type="tel"
                    name="telefon"
                    id="telefon"
                    placeholder="F.eks. 12345678"
                    pattern="[0-9]{8}"
                    maxlength="8"
                    minlength="8"
                    inputmode="numeric"
                    required
                    title="Telefonnummer må være 8 sifre, kun tall"
                >
            </div>

            <div class="field-group">
                <label for="stilling">Stilling</label>
                <input type="text" name="stilling" id="stilling" placeholder="F.eks. Daglig leder" required>
            </div>

            <hr class="field-divider">

            <button type="submit" name="InsertKontakt" class="submit-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Legg til kontaktperson
            </button>

        </form>
    </div>

    <!-- BACK LINK -->
    <a href="index.php" class="back-link">Tilbake til oversikten</a>

</main>

</body>
</html>