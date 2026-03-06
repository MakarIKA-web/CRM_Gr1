<?php
require_once "config.php";
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kjæledyrregister</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- ── NAVIGATION ── -->
    <nav>
        <a class="nav-logo" href="#">
            <!-- Paw icon -->
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

        <!-- ── HERO ── -->
        <section class="hero">
            <h1>Register</h1>
            <p>Oversikt over registrerte kjæledyr og eiere i systemet</p>
        </section>

        <!-- ── SECTION LABEL ── -->
        <p class="section-title">Kjæledyr</p>

        <!-- ── SEARCH / FILTER CONTROLS ── -->
        <div class="controls">
            <label class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Søk etter navn, type, rase…">
            </label>
            <select class="filter-select" id="typeFilter">
                <option value="">Alle typer</option>
            </select>
        </div>

        <!-- ── CARDS GRID ── -->
        <div class="pet-grid" id="petGrid">
            <?php
            $rows = [];

            if ($result->num_rows > 0) {
                $cardIndex = 0;
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;

                    // Colour/style variants cycling
                    $variants = ['', '', 'dark', '', ''];
                    $variantClass = $variants[$cardIndex % count($variants)];

                    // Make every 5th card wide
                    $wideClass = ($cardIndex % 5 === 4) ? ' wide' : '';

                    // Animal emoji fallback
                    $typeEmojis = [
                        'hund' => '🐕', 'katt' => '🐈', 'fugl' => '🦜',
                        'kanin' => '🐇', 'fisk' => '🐠', 'hamster' => '🐹'
                    ];
                    $typeLower = mb_strtolower($row['type'] ?? '');
                    $emoji = $typeEmojis[$typeLower] ?? '🐾';

                    $navn    = htmlspecialchars($row['navn']         ?? '', ENT_QUOTES);
                    $type    = htmlspecialchars($row['type']         ?? '', ENT_QUOTES);
                    $rase    = htmlspecialchars($row['rase']         ?? '', ENT_QUOTES);
                    $eier    = htmlspecialchars($row['eier_navn']    ?? '', ENT_QUOTES);
                    $fodt    = htmlspecialchars($row['foedselsdato'] ?? $row['fødselsdato'] ?? '', ENT_QUOTES);
                    $id      = (int) $row['id'];

                    echo "<div class='pet-card {$variantClass}{$wideClass}' data-navn='{$navn}' data-type='{$type}'>";
                    $bilde = htmlspecialchars($row['bilde'] ?? '', ENT_QUOTES);

                    if (!empty($bilde) && file_exists("uploads/" . $bilde)) {
                        echo "<div class='card-img-placeholder'>
                                <img src='uploads/{$bilde}' alt='{$navn}' class='card-img'>
                            </div>";
                    } else {
                        echo "<div class='card-img-placeholder'>{$emoji}</div>";
                    }
                    echo "  <div class='card-body'>";
                    echo "    <span class='card-badge'>{$type}</span>";
                    echo "    <div class='card-title'>{$navn}</div>";
                    echo "    <div class='card-meta'>{$rase}<br>{$eier}</div>";
                    echo "  </div>";
                    echo "</div>";

                    $cardIndex++;
                }
            }
            ?>
        </div>

        <!-- ── FULL DATA TABLE ── -->
        <section class="table-section">
            <h2>Alle registreringer</h2>
            <div class="table-wrapper">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Navn</th>
                            <th>Type</th>
                            <th>Rase</th>
                            <th>Fødselsdato</th>
                            <th>Eier</th>
                            <th>Telefon</th>
                            <th>Notater</th>
                            <th>Opprettet</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $id = (int) $row['id'];

                                if (isset($editing_id) && $editing_id == $id) {
                                    // ── EDIT ROW ──
                                    echo "<tr>";
                                    echo "<form method='post'>";
                                    echo "<td>{$id}</td>";
                                    foreach ([
                                        'navn'         => htmlspecialchars($row['navn']         ?? '', ENT_QUOTES),
                                        'type'         => htmlspecialchars($row['type']         ?? '', ENT_QUOTES),
                                        'rase'         => htmlspecialchars($row['rase']         ?? '', ENT_QUOTES),
                                        'fødselsdato'  => htmlspecialchars($row['fødselsdato']  ?? $row['foedselsdato'] ?? '', ENT_QUOTES),
                                        'eier_navn'    => htmlspecialchars($row['eier_navn']    ?? '', ENT_QUOTES),
                                        'telefon'      => htmlspecialchars($row['telefon']      ?? '', ENT_QUOTES),
                                        'notater'      => htmlspecialchars($row['notater']      ?? '', ENT_QUOTES),
                                        'opprettet_dato' => htmlspecialchars($row['opprettet_dato'] ?? '', ENT_QUOTES),
                                    ] as $field => $val) {
                                        echo "<td><input type='text' name='{$field}' value='{$val}' required></td>";
                                    }
                                    echo "<td colspan='2'>
                                            <button type='submit' class='btn btn-save'>
                                                <svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5'><polyline points='20 6 9 17 4 12'/></svg>
                                                Lagre
                                            </button>
                                          </td>";
                                    echo "</form>";
                                    echo "</tr>";
                                } else {
                                    // ── DISPLAY ROW ──
                                    $fodt = htmlspecialchars($row['foedselsdato'] ?? $row['fødselsdato'] ?? '', ENT_QUOTES);
                                    echo "<tr>";
                                    echo "<td>{$id}</td>";
                                    echo "<td>" . htmlspecialchars($row['navn']         ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['type']         ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['rase']         ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>{$fodt}</td>";
                                    echo "<td>" . htmlspecialchars($row['eier_navn']    ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['telefon']      ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['notater']      ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['opprettet_dato'] ?? '', ENT_QUOTES) . "</td>";
                                    echo "<td>
                                            <form method='GET' action='delete.php'>
                                                <input type='hidden' name='id' value='{$id}'>
                                                <button type='submit' class='btn btn-delete'>
                                                    <svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14H6L5 6'/><path d='M10 11v6M14 11v6'/></svg>
                                                    Slett
                                                </button>
                                            </form>
                                          </td>";
                                    echo "<td>
                                            <form method='GET' action='edit.php'>
                                                <input type='hidden' name='id' value='{$id}'>
                                                <button type='submit' class='btn btn-edit'>
                                                    <svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'/><path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'/></svg>
                                                    Rediger
                                                </button>
                                            </form>
                                          </td>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            echo "<tr><td colspan='11'>
                                    <div class='empty-state'>
                                        <svg viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.5'><circle cx='12' cy='12' r='10'/><line x1='12' y1='8' x2='12' y2='12'/><line x1='12' y1='16' x2='12.01' y2='16'/></svg>
                                        <p>Ingen data funnet</p>
                                    </div>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ── ADD NEW ── -->
        <div class="add-link">
            <a href="add.php" class="add-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Legg til nytt kjæledyr
            </a>
        </div>

    </main>

    <!-- Debug info (hidden from UI, visible in console) -->
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

    <!-- Live search + type filter -->
    <script>
        (function () {
            const searchInput = document.getElementById('searchInput');
            const typeFilter  = document.getElementById('typeFilter');
            const cards       = Array.from(document.querySelectorAll('#petGrid .pet-card'));
            const tableRows   = Array.from(document.querySelectorAll('#dataTable tbody tr'));

            // Populate type filter from card data
            const types = [...new Set(cards.map(c => c.dataset.type).filter(Boolean))];
            types.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t; opt.textContent = t;
                typeFilter.appendChild(opt);
            });

            function filterAll() {
                const query = searchInput.value.toLowerCase();
                const type  = typeFilter.value.toLowerCase();

                cards.forEach(card => {
                    const navn = (card.dataset.navn || '').toLowerCase();
                    const ct   = (card.dataset.type || '').toLowerCase();
                    const match = (!query || navn.includes(query) || ct.includes(query))
                               && (!type  || ct === type);
                    card.style.display = match ? '' : 'none';
                });

                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (!cells.length) return;
                    const text = Array.from(cells).map(c => c.textContent.toLowerCase()).join(' ');
                    const match = (!query || text.includes(query))
                               && (!type  || text.includes(type));
                    row.style.display = match ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterAll);
            typeFilter.addEventListener('change', filterAll);
        })();
    </script>

</body>
</html>