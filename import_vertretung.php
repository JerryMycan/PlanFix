<?php
// Verbindung zur MySQL-Datenbank herstellen
$servername = "localhost";
$username = "root"; // Anpassen je nach Umgebung
$password = "Kym7HEbeS6#"; // Anpassen je nach Umgebung
$dbname = "c1_planfix";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("❌ Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// **Pfad zur gefilterten Datei**
$file_path = "/var/www/html/planfix/vertretung.txt";

// Prüfen, ob die Datei existiert
if (!file_exists($file_path)) {
    die("❌ Fehler: Datei 'vertretung.txt' nicht gefunden.");
}

// **Datei mit Korrektur der Zeilenumbrüche einlesen**
$file_content = file_get_contents($file_path);
$file_content = str_replace("\r", "\n", $file_content);  // Falls Windows/Mac-Zeilenumbrüche existieren
$lines = preg_split("/\n+/", trim($file_content)); // Spaltet Zeilen anhand von neuen Zeilen

// Debug: Anzahl der erkannten Zeilen anzeigen
echo "<h2>🔍 Datei enthält " . count($lines) . " Zeilen.</h2>";

$imported_rows = 0;
$skipped_rows = 0;

foreach ($lines as $line) {
    echo "📌 Bearbeite Zeile: " . htmlspecialchars($line) . "<br>";

    // Prüfe auf verschiedene Trennzeichen (Tab, Komma, Semikolon)
    if (strpos($line, "\t") !== false) {
        $columns = explode("\t", trim($line));
        echo "🔹 Getrennt mit TAB ✅<br>";
    } elseif (strpos($line, ";") !== false) {
        $columns = explode(";", trim($line));
        echo "🔸 Getrennt mit SEMIKOLON ❌<br>";
    } elseif (strpos($line, ",") !== false) {
        $columns = explode(",", trim($line));
        echo "🔸 Getrennt mit KOMMA ❌<br>";
    } else {
        echo "❌ **Kein bekanntes Trennzeichen erkannt!**<br>";
        continue;
    }

    // Debug: Zeige die erkannten Spalten
    print_r($columns);
    echo "<br>";

    // Prüfen, ob die Zeile die richtige Anzahl an Spalten hat
    if (count($columns) < 4) {
        echo "⚠ Fehlerhafte Zeile übersprungen (zu wenig Spalten)<br>";
        $skipped_rows++;
        continue;
    }

    // Werte zuweisen
    $lehrer = trim($columns[0]);
    $datum_raw = trim($columns[1]);
    $datum = date("Y-m-d", strtotime($datum_raw)); // Datum ins SQL-Format umwandeln
    $stunde = intval(trim($columns[2]));
    $status = strtolower(trim($columns[3]));

    // **Prüfen, ob der Status gültig ist**
    if (!in_array($status, ['ausgefallen', 'vertritt'])) {
        echo "⚠ Ungültiger Status: '$status' in Zeile übersprungen<br>";
        $skipped_rows++;
        continue;
    }

    // **Daten in die DB einfügen**
    $stmt = $conn->prepare("INSERT INTO vertretungen (datum, stunde, status, lehrer) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $datum, $stunde, $status, $lehrer);

    if ($stmt->execute()) {
        echo "✅ Importiert: $datum | Stunde: $stunde | Status: $status | Lehrer: $lehrer <br>";
        $imported_rows++;
    } else {
        echo "❌ SQL-Fehler: " . $stmt->error . "<br>";
    }
}

// **Ergebnis anzeigen**
echo "<h2>Import abgeschlossen</h2>";
echo "✅ $imported_rows Zeilen erfolgreich importiert.<br>";
echo "⚠ $skipped_rows Zeilen übersprungen (fehlende Daten oder ungültiger Status).<br>";
echo "<br><a href='upload_and_filter.php'>⬅ Zurück zum Upload</a>";

// Verbindung schließen
$conn->close();
?>

