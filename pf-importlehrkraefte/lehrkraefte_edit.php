<?php
// ============================================================
// Lehrkräfte bearbeiten – Eingabeformular (sortiert nach Kürzel)
// ============================================================

require_once 'includes/db_connection.php';

// Lehrkräfte abrufen, alphabetisch nach Kürzel sortiert
$sql = "SELECT * FROM lehrkraefte ORDER BY kuerzel ASC";
$stmt = $pdo->query($sql);
$lehrkraefte = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Lehrkräfte bearbeiten</title>
    <link rel="stylesheet" href="css/lehrkraefte.css">
</head>
<body>
    <div class="container">
        <h1>Lehrkräfte bearbeiten</h1>

        <!-- Formular für alle Lehrkräfte -->
        <form method="post" action="logic/update_lehrkraefte.php">
            <table>
                <thead>
                    <tr>
                        <th># / Kürzel / ID</th>
                        <th>Vorname</th>
                        <th>Nachname</th>
                        <th>Lehrbefähigung</th>
                        <th>Fächer</th>
                        <th>E-Mail</th>
                        <th>Mobil</th>
                        <th>UWS</th>
                        <th>Max. Vertretung</th>
                        <th>Reserve</th>
                        <th>Aktiv</th>
                        <th>Bemerkung</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($lehrkraefte as $i => $lk): ?>
                    <tr>
                        <td>
                            <?= ($i + 1) ?>. <?= htmlspecialchars($lk['kuerzel']) ?> <!--(ID: <?= $lk['id'] ?>)-->
                            <input type="hidden" name="data[<?= $i ?>][kuerzel]" value="<?= htmlspecialchars($lk['kuerzel']) ?>">
                        </td>
                        <td><input type="text" name="data[<?= $i ?>][vorname]" value="<?= htmlspecialchars($lk['vorname']) ?>"></td>
                        <td><input type="text" name="data[<?= $i ?>][nachname]" value="<?= htmlspecialchars($lk['nachname']) ?>"></td>
                        <td><input type="text" name="data[<?= $i ?>][lehrbefaehigung]" value="<?= htmlspecialchars($lk['lehrbefaehigung']) ?>"></td>
                        <td><input type="text" name="data[<?= $i ?>][unterrichtsfaecher]" value="<?= htmlspecialchars($lk['unterrichtsfaecher']) ?>"></td>
                        <td><input type="text" name="data[<?= $i ?>][email_dienst]" value="<?= htmlspecialchars($lk['email_dienst']) ?>"></td>
                        <td><input type="text" name="data[<?= $i ?>][mobil]" value="<?= htmlspecialchars($lk['mobil']) ?>"></td>
                        <td><input type="number" step="0.5" name="data[<?= $i ?>][unterrichtswirksame_stunden]" value="<?= htmlspecialchars($lk['unterrichtswirksame_stunden']) ?>"></td>
                        <td><input type="number" step="0.5" name="data[<?= $i ?>][max_vertretung]" value="<?= htmlspecialchars($lk['max_vertretung']) ?>"></td>
                        <td>
                            <select name="data[<?= $i ?>][vertretungsreserve]">
                                <option value="0" <?= $lk['vertretungsreserve'] ? '' : 'selected' ?>>Nein</option>
                                <option value="1" <?= $lk['vertretungsreserve'] ? 'selected' : '' ?>>Ja</option>
                            </select>
                        </td>
                        <td>
                            <select name="data[<?= $i ?>][aktiv]">
                                <option value="1" <?= $lk['aktiv'] ? 'selected' : '' ?>>Aktiv</option>
                                <option value="0" <?= !$lk['aktiv'] ? 'selected' : '' ?>>Inaktiv</option>
                            </select>
                        </td>
                        <td><textarea name="data[<?= $i ?>][bemerkung]"><?= htmlspecialchars($lk['bemerkung']) ?></textarea></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <br>
            <button type="submit">Änderungen speichern</button>
        </form>
    </div>
</body>
</html>
