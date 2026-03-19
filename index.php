<?php
session_start();
require_once "config.php";

// Redirect to login if not logged in
if (!isset($_SESSION['ansatt_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="src/css/styl.css">
</head>
<body>
    <!-- Kunder -->
    <main>
        <h2>Kunder</h2>
        <p>Oversikt over alle kunder</p>

        <div class="controls" style="text-align:center; margin-bottom:10px;">
            <input type="text" id="kundeSearch" placeholder="Søk kunder..." />

            <select id="kundeTypeFilter">
                <option value="">Alle typer</option>
            </select>
        </div>

        <!-- Database table -->
        <table id="kundeTable" style="margin: auto;">
            <!-- Table header -->
            <tr><th>ID</th><th>Type</th><th>Firmanavn</th><th>Organisasjonsnummer</th><th>Adresse</th><th>Postnummer</th><th>Poststed</th><th>Opprettet dato</th></tr>

            <style>
                th {text-align: left;}
            </style>

            <!-- Table rows -->
            <?php

            $rows = [];

            // Check if there are results
            if ($resultkunder->num_rows > 0) {
                while($row = $resultkunder->fetch_assoc()) {

                    // --- Hent adressen fra adresse-tabellen ---
                    $adresseText = '';
                    $postnummerText = '';
                    $poststedText = '';

                    if (!empty($row['adresse_id'])) {
                        $adresseQuery = $conn->query("SELECT gate, postnummer FROM adresser WHERE adresse_id = " . intval($row['adresse_id']));
                        if ($adresseQuery && $adresseRow = $adresseQuery->fetch_assoc()) {
                            $adresseText = $adresseRow['gate'];
                            $postnummerText = $adresseRow['postnummer'];

                            // --- Hent poststed fra postnumre/steder ---
                            $postQuery = $conn->query("
                                SELECT s.poststed
                                FROM postnumre p, steder s
                                WHERE p.postnummer = '" . $conn->real_escape_string($postnummerText) . "'
                                AND p.sted_id = s.sted_id
                            ");
                            if ($postQuery && $postRow = $postQuery->fetch_assoc()) {
                                $poststedText = $postRow['poststed'];
                            }
                        }
                    }

                    $rows[] = $row; // fortsatt lagre raden for JS osv.

                    // --- Bygg tabellrad ---
                    if ($editing_kunde_id !== null && $editing_kunde_id == $row["kunde_id"]) {
                        echo "<tr>
                                <form method='post' action='rediger.php'>
                                    <td>" . htmlspecialchars($row["kunde_id"], ENT_QUOTES) . "</td>
                                    <td><input type='text' name='kundetype' value='" . htmlspecialchars($row["kundetype"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='firmanavn' value='" . htmlspecialchars($row["firmanavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='organisasjonsnummer' value='" . htmlspecialchars($row["organisasjonsnummer"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='adresse' value='" . htmlspecialchars($adresseText, ENT_QUOTES) . "' required></td>
                                    <td>" . htmlspecialchars($postnummerText) . "</td>
                                    <td>" . htmlspecialchars($poststedText) . "</td>
                                    <td>" . htmlspecialchars(date("Y-m-d", strtotime($row["opprettet_dato"]))) . "</td>
                                    <td>
                                        <a href='index.php'><button type='button'>Avbryt</button></a>
                                    </td>
                                    <td>
                                        <button type='submit' name='SaveKunde' value='" . $row["kunde_id"] . "'>Lagre</button>
                                    </td>
                                </form>
                            </tr>";
                    } else {
                        echo "<tr data-type='" . htmlspecialchars($row["kundetype"], ENT_QUOTES) . "'>
                                <td>" . htmlspecialchars($row["kunde_id"]) . "</td>
                                <td>" . htmlspecialchars($row["kundetype"]) . "</td>
                                <td>" . htmlspecialchars($row["firmanavn"]) . "</td>
                                <td>" . htmlspecialchars($row["organisasjonsnummer"]) . "</td>
                                <td>" . htmlspecialchars($adresseText) . "</td>
                                <td>" . htmlspecialchars($postnummerText) . "</td>
                                <td>" . htmlspecialchars($poststedText) . "</td>
                                <td>" . htmlspecialchars(date("Y-m-d", strtotime($row["opprettet_dato"]))) . "</td>
                                <td>
                                    <form method='GET' action='delete.php'>
                                        <input type='hidden' name='id' value='" . $row['kunde_id'] . "'>
                                        <button type='submit' onclick=\"return confirm('Er du sikker på at du vil slette denne kunden?')\">Slett</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='GET' action='redigerkunde.php'>
                                        <input type='hidden' name='id' value='{$row['kunde_id']}'>
                                        <button type='submit' class='btn btn-edit'>Rediger</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='8'>Ingen data funnet</td></tr>";
            }
            ?>
        </table>

        <a href="addkunde.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh; text-align: center;">Legg til ny kunde</article></a>
    </main>
    <!-- Kontaktpersoner -->
    <main>
        <h2>Kontaktpersoner</h2>
        <p>Oversikt over alle kontaktpersoner</p>

        <div class="controls" style="text-align:center; margin-bottom:10px;">
            <input type="text" id="kontaktSearch" placeholder="Søk kontaktpersoner..." />

            <select id="kontaktTypeFilter">
                <option value="">Alle typer</option>
            </select>
        </div>

        <!-- Database table -->
        <table id="kontaktpersonTable" style="margin: auto;">
            <!-- Table header -->
            <tr><th>Kontakt id</th><th>Firmanavn</th><th>Organisasjonsnummer</th><th>Fornavn</th><th>Etternavn</th><th>E-post</th><th>Stilling</th><th>Opprettet dato</th></tr>

            <style>
                th {text-align: left;}
            </style>

            <!-- Table rows -->
            <?php

            $rows = [];

            // Hent alle kunder for å vise firmanavn og for dropdown
            $kundeliste = [];
            $sqlAllKunder = "SELECT kunde_id, firmanavn, organisasjonsnummer, kundetype FROM kunder";
            $resultAllKunder = $conn->query($sqlAllKunder);
            $kundeliste = [];
            while ($k = $resultAllKunder->fetch_assoc()) {
                $kundeliste[$k['kunde_id']] = [
                    'firmanavn' => $k['firmanavn'],
                    'organisasjonsnummer' => $k['organisasjonsnummer'] ?? '',
                    'kundetype' => $k['kundetype'] ?? ''
                ];
            }

            // Check if there are results
            if ($resultkontaktpersoner->num_rows > 0) {

                while($row = $resultkontaktpersoner->fetch_assoc()) {

                    $rows[] = $row;

                    // Sjekk om denne raden redigeres (KONTAKTPERSONER)
                    if ($editing_kontakt_id !== null && $editing_kontakt_id == $row["kontakt_id"]) {
                        // Bygg dropdown for firmanavn
                        $kundeOptions = "";
                        foreach ($kundeliste as $kid => $fnavn) {
                            $selected = ($kid == $row["kunde_id"]) ? "selected" : "";
                            $kundeOptions .= "<option value='$kid' $selected>" . htmlspecialchars($fnavn['firmanavn'], ENT_QUOTES) . "</option>";
                        }

                        echo "<tr>
                                <form method='post' action='rediger.php'>
                                    <td>" . htmlspecialchars($row["kontakt_id"], ENT_QUOTES) . "</td>
                                    <td><select name='kunde_id'>$kundeOptions</select></td>
                                    <td><input type='text' name='fornavn' value='" . htmlspecialchars($row["fornavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='etternavn' value='" . htmlspecialchars($row["etternavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='email' name='epost' value='" . htmlspecialchars($row["epost"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='stilling' value='" . htmlspecialchars($row["stilling"] ?? '', ENT_QUOTES) . "'></td>
                                    <td>Oppdateres automatisk</td>
                                    <td>
                                        <a href='index.php'><button type='button'>Avbryt</button></a>
                                    </td>
                                    <td>
                                        <button type='submit' name='SaveKontakt' value='" . $row["kontakt_id"] . "'>Lagre</button>
                                    </td>
                                </form>
                              </tr>";
                    } else {
                        $kundeInfo = $kundeliste[$row['kunde_id']] ?? [
                            'firmanavn'=>'Ukjent',
                            'organisasjonsnummer'=>'Ukjent',
                            'kundetype'=>''
                        ];

                        $firmanavn = $kundeInfo['firmanavn'];
                        $orgnr = $kundeInfo['organisasjonsnummer'];
                        $kundetype = $kundeInfo['kundetype']; // du må hente denne fra kunder-tabellen

                        echo "<tr
                                data-type='" . htmlspecialchars($row["stilling"], ENT_QUOTES) . "'
                                data-kundetype='" . htmlspecialchars($kundetype, ENT_QUOTES) . "'>
                                <td>" . htmlspecialchars($row["kontakt_id"]) . "</td>
                                <td>" . htmlspecialchars($firmanavn) . "</td>
                                <td>" . htmlspecialchars($orgnr) . "</td>
                                <td>" . htmlspecialchars($row["fornavn"]) . "</td>
                                <td>" . htmlspecialchars($row["etternavn"]) . "</td>
                                <td>" . htmlspecialchars($row["epost"]) . "</td>
                                <td>" . htmlspecialchars($row["stilling"] ?? '') . "</td>
                                <td>" . htmlspecialchars(date("Y-m-d", strtotime($row["opprettet_dato"]))) . "</td>
                                <td>
                                    <form method='GET' action='delete.php'>
                                        <input type='hidden' name='kontakt_id' value='" . $row['kontakt_id'] . "'>
                                        <button type='submit' onclick=\"return confirm('Er du sikker på at du vil slette denne kontaktpersonen?')\"><svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14H6L5 6'/><path d='M10 11v6M14 11v6'/></svg> Slett</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='GET' action='redigerkontakt.php'>
                                        <input type='hidden' name='id' value='" . $row['kontakt_id'] . "'>
                                        <button type='submit' name='EditKontakt' class='btn btn-edit'>
                                            <svg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                                                <path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'/>
                                                <path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'/>
                                            </svg>
                                            Rediger
                                        </button>
                                    </form>
                                </td>
                            </tr>";
                    }
                }

            } else {
                echo "<tr><td colspan='9'>Ingen data funnet</td></tr>";
            }
            ?>
        </table>

        <a href="addkontakt.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh; text-align: center;">Legg til ny kontaktperson</article></a>
    </main>


    <script>
        const connectionInfo = {
            host: "<?php echo $conn->host_info; ?>",
            server: "<?php echo $conn->server_info; ?>",
            client: "<?php echo $conn->client_info; ?>",
            stat: "<?php echo $conn->stat(); ?>",
            info: "<?php echo $conn->info; ?>"
        };
        console.log(connectionInfo);
    </script>

    <script>
        const arrayValues = <?php echo json_encode($rows ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        console.log(arrayValues);
    </script>

    <script>
    (function(){

        const searchInput = document.getElementById("kundeSearch");
        const typeFilter  = document.getElementById("kundeTypeFilter");

        const rows = Array.from(document.querySelectorAll("#kundeTable tr")).slice(1);

        // hent alle typer
        const types = [...new Set(rows.map(r => r.dataset.type).filter(Boolean))];

        types.forEach(t => {
            const opt = document.createElement("option");
            opt.value = t;
            opt.textContent = t;
            typeFilter.appendChild(opt);
        });

        function filterRows(){

            const query = searchInput.value.trim().toLowerCase();
            const type  = typeFilter.value.trim().toLowerCase();

            rows.forEach(row => {

                // samle tekst fra alle celler i raden
                const text = Array.from(row.cells).map(c => c.textContent.toLowerCase()).join(' ');

                const rowType = (row.dataset.type || "").toLowerCase();

                const match =
                    (!query || text.includes(query)) &&
                    (!type || rowType === type);

                row.style.display = match ? "" : "none";

            });
        }

        searchInput.addEventListener("input", filterRows);
        typeFilter.addEventListener("change", filterRows);

    })();
    </script>

    <script>
    (function(){

        const searchInput = document.getElementById("kontaktSearch");
        const typeFilter  = document.getElementById("kontaktTypeFilter");

        const rows = Array.from(document.querySelectorAll("#kontaktpersonTable tr")).slice(1);

        // hent alle unike firmanavn fra kolonne 1
        const types = [...new Set(rows.map(r => r.cells[1]?.textContent.trim()).filter(Boolean))];

        types.forEach(t => {
            const opt = document.createElement("option");
            opt.value = t;
            opt.textContent = t;
            typeFilter.appendChild(opt);
        });

        function filterRows(){

            const query = searchInput.value.trim().toLowerCase();
            const type  = typeFilter.value.trim().toLowerCase();

            rows.forEach(row => {

                // samle tekst fra alle celler i raden
                const text = Array.from(row.cells).map(c => c.textContent.toLowerCase()).join(' ');

                // rowType basert på kolonne "Firmanavn" (index 1)
                const rowType = (row.cells[1]?.textContent.trim() || "").toLowerCase();

                const match =
                    (!query || text.includes(query)) &&
                    (!type || rowType === type);

                row.style.display = match ? "" : "none";

            });
        }

        searchInput.addEventListener("input", filterRows);
        typeFilter.addEventListener("change", filterRows);

    })();
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Hent alle rader med data-type
    const rows = document.querySelectorAll('tr[data-type]');

    rows.forEach(row => {
        const type = row.getAttribute('data-type');

        // Finn den 4. <td> (indeks 3)
        const td = row.querySelectorAll('td')[3];
        if (type === 'privat') {
            td.textContent = 'Privatperson';
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelectorAll('#kontaktpersonTable tr[data-kundetype]');

    rows.forEach(row => {
        const kundetype = (row.dataset.kundetype || "").toLowerCase();

        // Organisasjonsnummer = kolonne index 2
        const orgTd = row.querySelectorAll('td')[2];

        if (kundetype === 'privat') {
            orgTd.textContent = 'Privatperson';
        }
    });
});
</script>

</body>
</html>