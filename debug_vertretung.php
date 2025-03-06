<?php
// **Pfad zur Datei**
$file_path = "/var/www/html/planfix/vertretung.txt";

// Prüfen, ob die Datei existiert
if (!file_exists($file_path)) {
    die("❌ Fehler: Datei 'vertretung.txt' nicht gefunden.");
}

// Datei öffnen und die ersten 10 Zeilen ausgeben
$handle = fopen($file_path, "r");
if ($handle) {
    echo "<h2>🔍 Debugging: Zeilenstruktur der Datei</h2>";
    
    $line_count = 0;
    while (($line = fgets($handle)) !== false && $line_count < 10) {
        $line_count++;
        echo "📌 Zeile $line_count (Original): " . htmlspecialchars($line) . "<br>";

        // Prüfen, welche Trennzeichen enthalten sind
        if (strpos($line, "\t") !== false) {
            echo "🔹 Getrennt mit **TAB** ✅<br>";
            $columns = explode("\t", trim($line));
        } elseif (strpos($line, ";") !== false) {
            echo "🔸 Getrennt mit **SEMIKOLON** ❌<br>";
            $columns = explode(";", trim($line));
        } elseif (strpos($line, ",") !== false) {
            echo "🔸 Getrennt mit **KOMMA** ❌<br>";
            $columns = explode(",", trim($line));
        } else {
            echo "❌ **Kein bekanntes Trennzeichen erkannt!**<br>";
            continue;
        }

        // Zeige die Spalten an
        echo "🔎 Spalten: ";
        print_r($columns);
        echo "<br><br>";
    }
    fclose($handle);
} else {
    die("❌ Fehler: Datei konnte nicht geöffnet werden.");
}
?>
