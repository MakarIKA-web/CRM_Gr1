<?php
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- Kunder -->
    <main>
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

                // Output data for each row
                // $row is an associative array representing a single row from the database, where the keys are the column names
                // $result->fetch_assoc() fetches the next row from the result set as an associative array
                while($row = $resultkunder->fetch_assoc()) { // https://www.w3schools.com/php/func_mysqli_fetch_assoc.asp

                    // Add the row to the rows array
                    $rows[] = $row;

                    // If this row is being edited, render inputs inside the row
                    if (isset($editing_id) && $editing_id == $row["kunde_id"]) {
                        echo "<tr>
                                <form method='post'>
                                    <td>" . htmlspecialchars($row["kunde_id"], ENT_QUOTES) . "</td>
                                    <td><input type='text' name='kundetype' value='" . htmlspecialchars($row["kundetype"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='firmanavn' value='" . htmlspecialchars($row["firmanavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='organisasjonsnummer' value='" . htmlspecialchars($row["organisasjonsnummer"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='adresse' value='" . htmlspecialchars($row["adresse"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='opprettet_dato' value='" . htmlspecialchars($row["opprettet_dato"], ENT_QUOTES) . "' required></td>
                                    <td>
                                        <button type='submit' name='CancelEdit'>Avbryt</button>
                                    </td>
                                    <td>
                                        <button type='submit' name='SaveFunction' value='" . $row["kunde_id"] . "'>Lagre</button>
                                    </td>
                                </form>
                              </tr>";
                    } else {
                        // $row is an associative array representing a single row from the database, where the keys are the column names

                        echo "<tr>
                                <td>" . $row["kunde_id"] . "</td>
                                <td>" . $row["kundetype"] . "</td>
                                <td>" . $row["firmanavn"] . "</td>
                                <td>" . $row["organisasjonsnummer"] . "</td>
                                <td>" . $row["adresse"] . "</td>
                                <td>" . $row["opprettet_dato"] . "</td>
                                <td>
                                    <!-- vi bruker en GET-forespørsel for å slette, så vi må sende med ID-en til raden vi vil slette -->
                                    <form method='GET' action='delete.php'> <!-- sender vi brukeren til delete.php med ID-en -->
                                        <input type='hidden' name='id' value='" . $row['kunde_id'] . "'> <!-- skjuler ID-en i et input-felt -->
                                        <button type='submit'>Slett</button> <!-- sender ID-en til delete.php -->
                                    </form>
                                </td>
                                <td>
                                    <form method='post'>
                                        <button type='submit' name='EditFunction' value='" . $row["kunde_id"] . "'>Rediger</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                }

                // Nice tip for the database connection
                // $conn->close();

            // If there are no results
            } else {
                echo "<tr><td colspan='4'>Ingen data funnet</td></tr>";
            }
            ?>
        </table>

        <a href="add.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh;">Legg til ny kunde</article></a>
    </main>

    
    <!-- Kontaktpersoner -->
    <main>
        <!-- Database table -->
        <table style="margin: auto;">
            <!-- Table header -->
            <tr><th>Kontakt id</th><th>Kunde id</th><th>Fornavn</th><th>Etternavn</th><th>E-post</th><!-- <th>Adresse</th>--><th>Stilling</th><th>Opprettet dato</th></tr>

            <style>
                th {text-align: left;}
            </style>

            <!-- Table rows -->
            <?php

            $rows = [];

            // Check if there are results
            if ($resultkontaktpersoner->num_rows > 0) {

                // Output data for each row
                // $row is an associative array representing a single row from the database, where the keys are the column names
                // $result->fetch_assoc() fetches the next row from the result set as an associative array
                while($row = $resultkontaktpersoner->fetch_assoc()) { // https://www.w3schools.com/php/func_mysqli_fetch_assoc.asp

                    // Add the row to the rows array
                    $rows[] = $row;

                    // If this row is being edited, render inputs inside the row
                    if (isset($editing_id) && $editing_id == $row["kunde_id"]) {
                        echo "<tr>
                                <form method='post'>
                                    <td>" . htmlspecialchars($row["kontakt_id"], ENT_QUOTES) . "</td>
                                    <td>" . htmlspecialchars($row["kunde_id"], ENT_QUOTES) . "</td>
                                    <td><input type='text' name='fornavn' value='" . htmlspecialchars($row["fornavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='etternavn' value='" . htmlspecialchars($row["etternavn"], ENT_QUOTES) . "' required></td>
                                    <td><input type='text' name='epost' value='" . htmlspecialchars($row["epost"], ENT_QUOTES) . "' required></td>
                                    <!-- <td><input type='text' name='adresse' value='" . htmlspecialchars($row["adresse"], ENT_QUOTES) . "' required></td> -->
                                    <td><input type='text' name='opprettet_dato' value='" . htmlspecialchars($row["opprettet_dato"], ENT_QUOTES) . "' required></td>
                                    <td>
                                        <button type='submit' name='CancelEdit'>Avbryt</button>
                                    </td>
                                    <td>
                                        <button type='submit' name='SaveFunction' value='" . $row["kunde_id"] . "'>Lagre</button>
                                    </td>
                                </form>
                              </tr>";
                    } else {
                        // $row is an associative array representing a single row from the database, where the keys are the column names

                        // <td>" . $row["adresse"] . "</td>

                        echo "<tr>
                                <td>" . $row["kontakt_id"] . "</td>
                                <td>" . $row["kunde_id"] . "</td>
                                <td>" . $row["fornavn"] . "</td>
                                <td>" . $row["etternavn"] . "</td>
                                <td>" . $row["epost"] . "</td>
                                <td>" . $row["opprettet_dato"] . "</td>
                                <td>
                                    <!-- vi bruker en GET-forespørsel for å slette, så vi må sende med ID-en til raden vi vil slette -->
                                    <form method='GET' action='delete.php'> <!-- sender vi brukeren til delete.php med ID-en -->
                                        <input type='hidden' name='id' value='" . $row['kunde_id'] . "'> <!-- skjuler ID-en i et input-felt -->
                                        <button type='submit'>Slett</button> <!-- sender ID-en til delete.php -->
                                    </form>
                                </td>
                                <td>
                                    <form method='post'>
                                        <button type='submit' name='EditFunction' value='" . $row["kunde_id"] . "'>Rediger</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                }

                // Nice tip for the database connection
                // $conn->close();

            // If there are no results
            } else {
                echo "<tr><td colspan='4'>Ingen data funnet</td></tr>";
            }
            ?>
        </table>

        <a href="add.php"><article style="margin: auto; max-width: 327.812px; margin-top: 2vh;">Legg til ny kunde</article></a>
    </main>


    <script> 
        // Database connection information
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
        // Array of row values
        const arrayValues = <?php echo json_encode($rows ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        console.log(arrayValues);
    </script>
</body>
</html>