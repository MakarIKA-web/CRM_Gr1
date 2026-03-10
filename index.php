<?php
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body>
    <!-- Kunder -->
    <main>
        <h2>Kunder</h2>
        <!-- Database table -->
        <table style="margin: auto;">
            <!-- Table header -->
            <tr><th>ID</th><th>Type</th><th>Firmanavn</th><th>Organisasjonsnummer</th><th>Adresse</th><th>Opprettet dato</th></tr>

            <style>
                th {text-align: left;}
            </style>

            <!-- Table rows -->
            <?php

            $rows = [];

            // Check if there are results
            if ($resultkunder->num_rows > 0) {

                while($row = $resultkunder->fetch_assoc()) {

                    $rows[] = $row;

                    // Sjekk om denne raden redigeres (KUNDER)
                    if ($editing_kunde_id !== null && $editing_kunde_id == $row["kunde_id"]) {
                        echo "<tr>
                                <form method='post' action='rediger.php'>
                                    <td>" . htmlspecialchars($row["kunde_id"], ENT_QUOTES) . "</td>
                                    <td><input type='text' name='kundetype' value='" . htmlspecialchars($row["kundetype"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='firmanavn' value='" . htmlspecialchars($row["firmanavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='organisasjonsnummer' value='" . htmlspecialchars($row["organisasjonsnummer"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='adresse' value='" . htmlspecialchars($row["adresse"], ENT_QUOTES) . "' required></td>
                                    <td>Oppdateres automatisk</td>
                                    <td>
                                        <a href='index.php'><button type='button'>Avbryt</button></a>
                                    </td>
                                    <td>
                                        <button type='submit' name='SaveKunde' value='" . $row["kunde_id"] . "'>Lagre</button>
                                    </td>
                                </form>
                              </tr>";
                    } else {
                        echo "<tr>
                                <td>" . htmlspecialchars($row["kunde_id"]) . "</td>
                                <td>" . htmlspecialchars($row["kundetype"]) . "</td>
                                <td>" . htmlspecialchars($row["firmanavn"]) . "</td>
                                <td>" . htmlspecialchars($row["organisasjonsnummer"]) . "</td>
                                <td>" . htmlspecialchars($row["adresse"]) . "</td>
                                <td>" . htmlspecialchars($row["opprettet_dato"]) . "</td>
                                <td>
                                    <form method='GET' action='delete.php'>
                                        <input type='hidden' name='id' value='" . $row['kunde_id'] . "'>
                                        <button type='submit' onclick=\"return confirm('Er du sikker på at du vil slette denne kunden?')\">Slett</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='post' action='rediger.php'>
                                        <button type='submit' name='EditKunde' value='" . $row["kunde_id"] . "'>Rediger</button>
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

        <a href="add.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh; text-align: center;">Legg til ny kunde</article></a>
    </main>
    <!-- Kontaktpersoner -->
    <main>
        <h2>Kontaktpersoner</h2>
        <!-- Database table -->
        <table style="margin: auto;">
            <!-- Table header -->
            <tr><th>Kontakt id</th><th>Firmanavn</th><th>Fornavn</th><th>Etternavn</th><th>E-post</th><th>Stilling</th><th>Opprettet dato</th></tr>

            <style>
                th {text-align: left;}
            </style>

            <!-- Table rows -->
            <?php

            $rows = [];

            // Hent alle kunder for å vise firmanavn og for dropdown
            $kundeliste = [];
            $sqlAllKunder = "SELECT kunde_id, firmanavn FROM kunder";
            $resultAllKunder = $conn->query($sqlAllKunder);
            while ($k = $resultAllKunder->fetch_assoc()) {
                $kundeliste[$k['kunde_id']] = $k['firmanavn'];
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
                            $kundeOptions .= "<option value='$kid' $selected>" . htmlspecialchars($fnavn, ENT_QUOTES) . "</option>";
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
                        $firmanavn = isset($kundeliste[$row["kunde_id"]]) ? $kundeliste[$row["kunde_id"]] : "Ukjent";
                        echo "<tr>
                                <td>" . htmlspecialchars($row["kontakt_id"]) . "</td>
                                <td>" . htmlspecialchars($firmanavn) . "</td>
                                <td>" . htmlspecialchars($row["fornavn"]) . "</td>
                                <td>" . htmlspecialchars($row["etternavn"]) . "</td>
                                <td>" . htmlspecialchars($row["epost"]) . "</td>
                                <td>" . htmlspecialchars($row["stilling"] ?? '') . "</td>
                                <td>" . htmlspecialchars($row["opprettet_dato"]) . "</td>
                                <td>
                                    <form method='GET' action='delete.php'>
                                        <input type='hidden' name='kontakt_id' value='" . $row['kontakt_id'] . "'>
                                        <button type='submit' onclick=\"return confirm('Er du sikker på at du vil slette denne kontaktpersonen?')\">Slett</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='post' action='rediger.php'>
                                        <button type='submit' name='EditKontakt' value='" . $row["kontakt_id"] . "'>Rediger</button>
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

        <a href="add.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh; text-align: center;">Legg til ny kontaktperson</article></a>
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
</body>
</html>