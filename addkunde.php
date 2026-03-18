<?php
require_once "config.php";

$orgnr_error = $_GET['orgnr_error'] ?? '';
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="src/css/styl.css">
    <title>Nytt Kunde</title>
</head>
<body class="form-page">

<main>

    <!-- HERO -->
    <section class="form-hero">
        <h1>Nytt kunde</h1>
        <p>Fyll inn informasjon om kunden</p>
    </section>

    <!-- FORM CARD -->
    <div class="form-card">
        <form method="post" action="requests.php" id="addForm" enctype="multipart/form-data">

            <!-- sjekk om den eksisterer allerede -->
            <div class="field-group">
                <label for="firmanavn">Firmanavn</label>
                <input type="text" name="firmanavn" id="firmanavn" placeholder="F.eks. Rema 1000" required>
            </div>

            <div class="field-group">
                <label for="kundetype">Kundetype</label>
                <select name="kundetype" id="kundetype" required>
                    <option value="">Velg type</option>
                    <option value="privat">Privat</option>
                    <option value="bedrift">Bedrift</option>
                </select>
            </div>

            <div class="field-group" id="orgnr-field">
                <label for="organisasjonsnummer">Organisasjonsnummer</label>
                <input type="text" name="organisasjonsnummer" id="organisasjonsnummer" placeholder="F.eks. 123456789"
                    minlength="9" maxlength="9">
            </div>

            <div class="field-group">
                <label for="adresse">Adresse</label>
                <input type="text" name="adresse" id="adresse" placeholder="F.eks. Storgata 1" required>
            </div>

            <div class="field-group">
                <label for="postnummer">Postnummer</label>
                <input type="text" name="postnummer" id="postnummer" placeholder="F.eks. 1234" required minlength="4" maxlength="4">
            </div>

            <div class="field-group">
                <label for="poststed">Poststed</label>
                <input type="text" name="poststed" id="poststed" placeholder="F.eks. Oslo" required>
            </div>

            <hr class="field-divider">

            <!-- KONTAKTPERSONER -->
            <h2>Kontaktpersoner</h2>
            <div id="kontaktperson-container">

                <div class="kontaktperson">
                    <div class="field-group">
                        <label>Fornavn</label>
                        <input type="text" name="kontaktperson_fornavn[]" placeholder="F.eks. Ola" required>
                    </div>
                    <div class="field-group">
                        <label>Etternavn</label>
                        <input type="text" name="kontaktperson_etternavn[]" placeholder="F.eks. Nordmann" required>
                    </div>
                    <div class="field-group">
                        <label>E-post</label>
                        <input type="email" name="kontaktperson_epost[]" placeholder="F.eks. ola@nordmann.no" required>
                    </div>
                    <div class="field-group">
                        <label>Telefon</label>
                        <input type="text" name="kontaktperson_telefon[]" placeholder="F.eks. 12345678" pattern="[0-9]{8}" maxlength="8" minlength="8" inputmode="numeric" required>
                    </div>
                    <div class="field-group">
                        <label>Stilling</label>
                        <input type="text" name="kontaktperson_stilling[]" placeholder="F.eks. Daglig leder" required>
                    </div>
                    <button type="button" class="remove-contact-btn">Fjern kontaktperson</button>
                    <hr class="field-divider">
                </div>

            </div>

            <button type="button" class="add-contact-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Legg til flere kontaktpersoner
            </button>

            <hr class="field-divider">

            <button type="submit" name="InsertFunction" class="submit-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Legg til kunde
            </button>

        </form>
    </div>

    <!-- BACK LINK -->
    <a href="index.php" class="back-link">Tilbake til oversikten</a>

</main>

<!-- DYNAMIC CONTACT PERSON SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addBtn = document.querySelector('.add-contact-btn');
    const container = document.getElementById('kontaktperson-container');

    addBtn.addEventListener('click', () => {
        // Clone first kontaktperson
        const firstKontakt = container.querySelector('.kontaktperson');
        const newKontakt = firstKontakt.cloneNode(true);

        // Clear all input values
        newKontakt.querySelectorAll('input').forEach(input => input.value = '');

        container.appendChild(newKontakt);
    });

    // Remove a contact person
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const kundetypeSelect = document.getElementById('kundetype');
    const firmanavnInput = document.getElementById('firmanavn');
    const orgnrInput = document.getElementById('organisasjonsnummer');
    const orgnrField = document.getElementById('orgnr-field');

    const fornavnInput = document.querySelector('input[name="kontaktperson_fornavn[]"]');
    const etternavnInput = document.querySelector('input[name="kontaktperson_etternavn[]"]');

    function updateFirmanavn() {
        if (kundetypeSelect.value === 'privat') {
            // Sett navn automatisk
            firmanavnInput.value = `${fornavnInput.value} ${etternavnInput.value}`.trim();
            firmanavnInput.readOnly = true;

            // Skjul organisasjonsnummer
            orgnrField.style.display = 'none';
            orgnrInput.value = '';
            orgnrInput.required = false;

        } else {
            firmanavnInput.readOnly = false;

            // Vis organisasjonsnummer
            orgnrField.style.display = 'block';
            orgnrInput.required = true;
        }
    }

    kundetypeSelect.addEventListener('change', updateFirmanavn);
    fornavnInput.addEventListener('input', updateFirmanavn);
    etternavnInput.addEventListener('input', updateFirmanavn);
});
</script>

</body>
</html>