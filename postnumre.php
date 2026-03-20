<?php
session_start();
require_once "config.php";

// Kun admin eller support
if (!isset($_SESSION['ansatt_id']) || !in_array($_SESSION['rolle'], ['admin','support'])) {
    header("Location: login.php");
    exit;
}

// Hent alle postnummer med poststed
$sql = "SELECT p.postnummer, s.poststed 
        FROM postnumre p
        JOIN steder s ON p.sted_id = s.sted_id
        ORDER BY p.postnummer ASC";
$result = $conn->query($sql);

$editing_postnummer = $_GET['edit'] ?? null;
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Postnummer Oversikt</title>
    <link rel="stylesheet" href="src/css/style.css">
</head>
<body>
    <h2>Oversikt over postnummer og poststed</h2>
    <p>Søk og rediger postnummer (kun admin/support kan redigere)</p>

    <div style="margin-bottom:10px;">
        <input type="text" id="searchInput" placeholder="Søk postnummer eller poststed..." />
    </div>

    <table id="postTable" border="1" style="margin:auto;">
        <tr>
            <th>Postnummer</th>
            <th>Poststed</th>
            <th>Handling</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <?php if ($editing_postnummer && $editing_postnummer == $row['postnummer']): ?>
                    <tr>
                        <form method="post" action="rediger_post.php">
                            <td><input type="text" name="postnummer" value="<?= htmlspecialchars($row['postnummer']) ?>" required readonly></td>
                            <td><input type="text" name="poststed" value="<?= htmlspecialchars($row['poststed']) ?>" required></td>
                            <td>
                                <button type="submit" name="save" value="<?= htmlspecialchars($row['postnummer']) ?>">Lagre</button>
                                <a href="postnumre.php"><button type="button">Avbryt</button></a>
                            </td>
                        </form>
                    </tr>
                <?php else: ?>
                    <tr data-post="<?= htmlspecialchars($row['postnummer']) ?>" data-sted="<?= htmlspecialchars($row['poststed']) ?>">
                        <td><?= htmlspecialchars($row['postnummer']) ?></td>
                        <td><?= htmlspecialchars($row['poststed']) ?></td>
                        <td>
                            <a href="postnumre.php?edit=<?= htmlspecialchars($row['postnummer']) ?>"><button>Rediger</button></a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">Ingen postnummer funnet</td></tr>
        <?php endif; ?>
    </table>

    <a href="index.php" class="back-link">Tilbake til oversikten</a>

    <script>
        const searchInput = document.getElementById('searchInput');
        const rows = Array.from(document.querySelectorAll('#postTable tr')).slice(1);

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            rows.forEach(row => {
                const postnummer = row.dataset.post || '';
                const poststed = row.dataset.sted || '';
                const match = postnummer.toLowerCase().includes(query) || poststed.toLowerCase().includes(query);
                row.style.display = match ? '' : 'none';
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const html = document.documentElement;
        const savedTheme = localStorage.getItem('tema');
        if (savedTheme) {
            html.classList.remove('light', 'dark');
            html.classList.add(savedTheme);
        } else {
            html.classList.add('dark'); // default tema
        }

        const btn = document.getElementById('toggleThemeBtn');
        if (btn) {
            btn.addEventListener('click', () => {
                const newTheme = html.classList.contains('light') ? 'dark' : 'light';
                html.classList.remove('light', 'dark');
                html.classList.add(newTheme);
                localStorage.setItem('tema', newTheme);
            });
        }
    });
    </script>

</body>
</html>