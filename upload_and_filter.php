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

    // Datei einlesen & Formatierung korrigieren (Erzwingen von Zeilenumbrüchen)
    $file_content = file_get_contents($file_tmp);
    $file_content = str_replace("\r", "\n", $file_content);  // Korrigiert Windows/Mac-Zeilenumbrüche
    $lines = preg_split("/\n+/", trim($file_content));  // Korrekte Zeilenaufteilung

    // Gefilterte Daten vorbereiten
    $filtered_lines = [];

    foreach ($lines as $line) {
        $columns = explode("\t", trim($line)); // Tab als Trennzeichen

        // Prüfen, ob mindestens 4 Spalten existieren
        if (count($columns) < 4) {
            continue;
        }

        // Die vierte Spalte auf "ausgefallen***" oder "vertritt***" prüfen
        if (preg_match("/^(ausgefallen|vertritt).*/i", trim($columns[3]))) {
            $filtered_lines[] = implode("\t", $columns);
        }
    }

    // Falls keine Zeilen gefiltert wurden
    if (empty($filtered_lines)) {
        die("⚠ Keine passenden Einträge gefunden. Datei enthält keine relevanten Daten.");
    }

    // Gefilterte Daten in `vertretung.txt` speichern
    file_put_contents($target_file, implode("\n", $filtered_lines));

    echo "✅ Datei erfolgreich verarbeitet und gespeichert unter: <strong>$target_file</strong><br>";
    echo "<a href='/planfix/vertretung.txt' download>📥 Gefilterte Datei herunterladen</a>";
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
