<?php
// Zielverzeichnis für die gefilterte Datei
$target_directory = "/var/www/html/planfix/";
$target_file = $target_directory . "vertretung.txt";

// Prüfen, ob das Verzeichnis existiert, sonst erstellen
if (!is_dir($target_directory)) {
    mkdir($target_directory, 0777, true);
}

// Falls eine Datei hochgeladen wurde
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_tmp = $_FILES["file"]["tmp_name"];
    $file_name = $_FILES["file"]["name"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Debugging: Zeige Dateipfad und Dateiname
    echo "Hochgeladene Datei: $file_name<br>";
    echo "Temporärer Pfad: $file_tmp<br>";

    // Nur TXT-Dateien erlauben
    if ($file_ext !== "txt") {
        die("❌ Fehler: Nur .TXT-Dateien sind erlaubt.");
    }

    // Datei in Zielverzeichnis speichern (Original, zur Sicherheit)
    $uploaded_file = $target_directory . "original_" . basename($file_name);
    if (!move_uploaded_file($file_tmp, $uploaded_file)) {
        die("❌ Fehler: Die Datei konnte nicht gespeichert werden.");
    }

    // Debugging: Datei erfolgreich gespeichert
    echo "✅ Datei erfolgreich gespeichert als: $uploaded_file<br>";

    // Datei einlesen, filtern und speichern
    $filtered_lines = [];
    $handle = fopen($uploaded_file, "r");

    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $columns = explode("\t", trim($line)); // Tabulator als Trennzeichen
            if (count($columns) > 3 && in_array(strtolower(trim($columns[3])), ["ausgefallen", "vertritt"])) {
                $filtered_lines[] = $line;
            }
        }
        fclose($handle);
    } else {
        die("❌ Fehler: Datei konnte nicht geöffnet werden.");
    }

    // Gefilterte Daten speichern
    if (file_put_contents($target_file, implode("", $filtered_lines))) {
        echo "✅ Gefilterte Datei gespeichert unter: <strong>$target_file</strong><br>";
        echo "<a href='/planfix/vertretung.txt' download>📥 Gefilterte Datei herunterladen</a>";
    } else {
        die("❌ Fehler: Gefilterte Datei konnte nicht gespeichert werden.");
    }
} else {
    echo "⚠ Bitte eine Datei hochladen.";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Vertretungsdatei Hochladen & Filtern</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .upload-box {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 2px dashed #007bff;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Vertretungsdatei Hochladen & Filtern</h2>
    <div class="upload-box">
        <form action="upload_and_filter.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br><br>
            <button type="submit">📂 Hochladen & Filtern</button>
        </form>
    </div>
</body>
</html>
