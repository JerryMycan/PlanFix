<!-- index.php -->
<?php
session_start();
$feedback = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Lehrkräfte-Import</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Lehrkräfte-Import (.xlsx oder .csv)</h1>

        <?php if ($feedback): ?>
            <div class="feedback"><?= htmlspecialchars($feedback) ?></div>
        <?php endif; ?>

        <form action="logic/import_lehrkraefte.php" method="post" enctype="multipart/form-data">
            <label for="file">Datei auswählen:</label>
            <input type="file" name="file" id="file" accept=".xlsx,.csv" required>
            <button type="submit">Import starten</button>
        </form>
    </div>
</body>
</html>
