<?php
require_once "config.php";
?>

<?php
// Hent dyretyper
$dyretyper = $conn->query("SELECT * FROM dyretype ORDER BY navn")->fetch_all(MYSQLI_ASSOC);

// Hent raser (kan oppdateres dynamisk via JS/AJAX basert på valgt type)
$raser = $conn->query("SELECT * FROM rase ORDER BY navn")->fetch_all(MYSQLI_ASSOC);
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

    <!-- NAVIGATION -->
    <nav>
        <a class="nav-logo" href="index.php">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C9.8 2 8 3.8 8 6s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm-6 6C4.3 8 3 9.3 3 11s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zm12 0c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3zM6 15c-2.2 0-4 1.8-4 4 0 1.7 1.7 3 4 3 1.3 0 2.4-.5 3.1-1.3C9.7 21.5 10.8 22 12 22s2.3-.5 2.9-1.3C15.6 21.5 16.7 22 18 22c2.3 0 4-1.3 4-3 0-2.2-1.8-4-4-4-1.1 0-2.1.4-2.8 1.1C14.5 15.4 13.3 15 12 15s-2.5.4-3.2 1.1C8.1 15.4 7.1 15 6 15z"/>
            </svg>
            Kjæledyrregister
        </a>
        <!-- <button class="nav-menu-btn" aria-label="Meny">
            <span></span><span></span><span></span>
        </button> -->
    </nav>

    <main>

        <!-- HERO -->
        <section class="form-hero">
            <h1>Nytt kjæledyr</h1>
            <p>Fyll inn informasjon om kjæledyret og eieren</p>
        </section>

        <!-- PROGRESS DOTS -->
        <div class="form-steps">
            <div class="step-dot active" id="dot1"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        <!-- MESSAGE BANNER -->
        <?php if (!empty($message)) : ?>
            <div class="message-banner <?= (strpos(strtolower($message), 'feil') !== false || strpos(strtolower($message), 'error') !== false) ? 'error' : 'success' ?>">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <?php if (strpos(strtolower($message), 'feil') !== false || strpos(strtolower($message), 'error') !== false): ?>
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    <?php else: ?>
                        <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/>
                    <?php endif; ?>
                </svg>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- FORM CARD -->
        <div class="form-card">
            <form method="post" id="addForm" enctype="multipart/form-data">

                <div class="field-group">
                    <label for="navn">Navn på kjæledyret</label>
                    <input type="text" name="navn" id="navn" placeholder="F.eks. Bella" required>
                </div>

                <div class="field-row">
                    <div class="field-group">
                        <label for="dyretype">Dyretype</label>
                        <select name="dyretype_id" id="dyretype" required>
                            <option value="">Velg type</option>
                            <?php foreach ($dyretyper as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['navn']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="rase">Rase</label>
                        <select name="rase_id" id="rase" required>
                            <option value="">Velg rase</option>
                            <?php foreach ($raser as $rase): ?>
                                <option value="<?= $rase['id'] ?>" data-dyretype="<?= $rase['dyretype_id'] ?>">
                                    <?= htmlspecialchars($rase['navn']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label for="foedselsdato">Fødselsdato</label>
                    <input type="date" name="foedselsdato" id="foedselsdato" max="<?= date('Y-m-d') ?>" required>
                </div>

                <hr class="field-divider">

                <div class="field-group">
                    <label for="eier_navn">Eierens navn</label>
                    <input type="text" name="eier_navn" id="eier_navn" placeholder="F.eks. Kari Nordmann" required>
                </div>

                <div class="field-group">
                    <label for="telefon">Telefonnummer</label>
                    <input
                        type="text"
                        name="telefon"
                        id="telefon"
                        placeholder="12345678"
                        pattern="[0-9]{8}"
                        maxlength="8"
                        minlength="8"
                        inputmode="numeric"
                        required>
                </div>

                <hr class="field-divider">

                <div class="field-group">
                    <label for="notater">Notater</label>
                    <input type="text" name="notater" id="notater" placeholder="Eventuelle merknader…" required>
                </div>

                <hr class="field-divider">

                <div class="field-group">
                    <label for="bilde">Last opp bilde</label>
                    <input type="file" name="bilde" id="bilde-input" required>
                </div>

                <button type="submit" name="InsertFunction" class="submit-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Legg til kjæledyr
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