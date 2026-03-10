<?php
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="src/style.css">
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
            <form method="post" id="addForm" enctype="multipart/form-data">

                <div class="field-group">
                    <label for="navn">Firmanavn</label>
                    <input type="text" name="navn" id="navn" placeholder="F.eks. Rema 1000" required>
                </div>

                <div class="field-group">
                    <label for="kundetype">Kundetype</label>
                    <select name="kundetype" id="kundetype" required>
                        <option value="">Velg type</option>
                        <option value="privat">Privat</option>
                        <option value="bedrift">Bedrift</option>
                    </select>
                </div>

                <div class="field-group">
                    <label for="organisasjonsnummer">Organisasjonsnummer</label>
                    <input type="text" name="organisasjonsnummer" id="organisasjonsnummer" placeholder="F.eks. 123456789" required>
                </div>

                <div class="field-group">
                    <label for="opprettet_dato">Opprettet dato</label>
                    <input type="date" name="opprettet_dato" id="opprettet_dato" max="<?= date('Y-m-d') ?>" required>
                </div>

                <hr class="field-divider">

                <!-- kontaktpersoner -->
                <div class="field-group">
                    <label for="kontaktperson_navn">Kontaktpersonens navn</label>
                    <input type="text" name="kontaktperson_navn" id="kontaktperson_navn" placeholder="F.eks. Ola Nordmann" required>
                </div>

                <div class="field-group">
                    <label for="kontaktperson_fornavn">Kontaktpersonens fornavn</label>
                    <input type="text" name="kontaktperson_fornavn" id="kontaktperson_fornavn" placeholder="F.eks. Ola" required>
                </div>

                <div class="field-group">
                    <label for="kontaktperson_etternavn">Kontaktpersonens etternavn</label>
                    <input type="text" name="kontaktperson_etternavn" id="kontaktperson_etternavn" placeholder="F.eks. Nordmann" required>
                </div>

                <div class="field-group">
                    <label for="kontaktperson_epost">Kontaktpersonens e-post</label>
                    <input type="email" name="kontaktperson_epost" id="kontaktperson_epost" placeholder="F.eks. ola@nordmann.no" required>
                </div>

                <div class="field-group">
                    <label for="kontaktperson_telefon">Kontaktpersonens telefon</label>
                    <input type="text" name="kontaktperson_telefon" id="kontaktperson_telefon" placeholder="F.eks. 12345678" pattern="[0-9]{8}" maxlength="8" minlength="8" inputmode="numeric" required>
                </div>

                <div class="field-group">
                    <label for="kontaktperson_stilling">Kontaktpersonens stilling</label>
                    <input type="text" name="kontaktperson_stilling" id="kontaktperson_stilling" placeholder="F.eks. Daglig leder" required>
                </div>

                <!-- Legg til flere kontaktpersoner -->
                <button type="button" class="add-contact-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>

                <hr class="field-divider">

                <hr class="field-divider">

                <button type="submit" name="InsertFunction" class="submit-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Legg til kontaktperson
                </button>

            </form>
        </div>

        <!-- BACK LINK -->
        <a href="index.php" class="back-link">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Tilbake til oversikten
        </a>

    </main>

    <!-- Debug console – unchanged from original -->
    <script>
        const connectionInfo = {
            host:   "<?php echo addslashes($conn->host_info); ?>",
            server: "<?php echo addslashes($conn->server_info); ?>",
            client: "<?php echo addslashes($conn->client_info); ?>",
            stat:   "<?php echo addslashes($conn->stat()); ?>",
            info:   "<?php echo addslashes($conn->info ?? ''); ?>"
        };
        console.log('DB connection:', connectionInfo);

        const arrayValues = <?php echo json_encode($rows ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        console.log('Rows:', arrayValues);
    </script>

    <!-- Dynamic rase filter – logic unchanged from original -->
    <script>
        const raser          = <?php echo json_encode($raser); ?>;
        const dyretypeSelect = document.getElementById('dyretype');
        const raseSelect     = document.getElementById('rase');

        dyretypeSelect.addEventListener('change', () => {
            const typeId = parseInt(dyretypeSelect.value);
            Array.from(raseSelect.options).forEach(option => {
                if (option.value === '') return; // behold "Velg rase"
                option.style.display = (parseInt(option.dataset.dyretype) === typeId) ? 'block' : 'none';
            });
            raseSelect.value = '';
        });

        // Progress dot animation
        const dots   = [document.getElementById('dot1'), document.getElementById('dot2'), document.getElementById('dot3')];
        const fields = document.querySelectorAll('#addForm input, #addForm select');

        function updateDots() {
            const filled   = Array.from(fields).filter(f => f.value.trim() !== '').length;
            const progress = filled / fields.length;
            dots[0].classList.toggle('active', true);
            dots[1].classList.toggle('active', progress > 0.4);
            dots[2].classList.toggle('active', progress > 0.8);
        }

        fields.forEach(f => f.addEventListener('input',  updateDots));
        fields.forEach(f => f.addEventListener('change', updateDots));
    </script>

</body>
</html>