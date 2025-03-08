<?php
// **Fehlermeldungen aktivieren**
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// **Verbindung zur MySQL-Datenbank herstellen**
$servername = "localhost";
$username = "root"; 
$password = "Kym7HEbeS6#"; 
$dbname = "c1_planfix"; 

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->query("USE c1_planfix;"); // Sicherstellen, dass die richtige DB genutzt wird

// **Verbindung prüfen**
if ($conn->connect_error) {
    die("❌ Verbindung fehlgeschlagen: " . $conn->connect_error);
} else {
    echo "✅ Verbindung zur Datenbank erfolgreich hergestellt.<br>";
}

// **Pfad zur CSV-Datei**
$file_path = "/var/www/html/planfix/vertretung.csv";

// **Prüfen, ob die Datei existiert**
if (!file_exists($file_path)) {
    die("❌ Fehler: Datei 'vertretung.csv' wurde nicht gefunden.");
}

// **Datei öffnen**
$handle = fopen($file_path, "r");
if (!$handle) {
    die("❌ Fehler: Datei konnte nicht geöffnet werden.");
}

// **Kopfzeile auslesen (und überspringen)**
$header = fgetcsv($handle, 1000, ";");
echo "📌 CSV-Spalten erkannt: " . implode(", ", $header) . "<br>";

$imported_rows = 0;
$skipped_rows = 0;

// **Daten aus der Datei einlesen und speichern**
while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    echo "🔹 Zeile gelesen: ";
    print_r($data);
    echo "<br>";

    // **Falls weniger als 9 Spalten, Zeile überspringen**
    if (count($data) < 9) {
        echo "⚠ Zeile übersprungen (zu wenig Spalten): " . implode(" | ", $data) . "<br>";
        $skipped_rows++;
        continue;
    }

    // **Lehrer-Kürzel auslesen & unsichtbare Zeichen entfernen**
    $lehrer_kuerzel = trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $data[0]));

    // **Falls Lehrer-Kürzel leer ist, Standardwert setzen**
    if (empty($lehrer_kuerzel)) {
        $lehrer_kuerzel = "UNBEKANNT";
    }

    // **Weitere Werte zuweisen**
    $datum = date("Y-m-d", strtotime(trim($data[1])));
    $stunde = trim($data[2]);
    $status = strtolower(trim($data[3]));
    $diff = is_numeric($data[4]) ? floatval($data[4]) : NULL;
    $vertritt_in = trim($data[5]) ?: NULL;
    $zusatzinfo_1 = trim($data[6]) ?: NULL;
    $zusatzinfo_2 = trim($data[7]) ?: NULL;
    $zusatzinfo_3 = trim($data[8]) ?: NULL;

    echo "📊 Werte für INSERT: Lehrer: $lehrer_kuerzel, Datum: $datum, Stunde: $stunde, Status: $status, Diff: $diff, Vertritt: $vertritt_in, Zusatzinfo1: $zusatzinfo_1, Zusatzinfo2: $zusatzinfo_2, Zusatzinfo3: $zusatzinfo_3 <br>";

    // **SQL-Befehl vorbereiten**
    $stmt = $conn->prepare("
        INSERT INTO `vertretungen` 
        (`lehrer_kuerzel`, `datum`, `stunde`, `status`, `diff`, `vertritt_in`, `zusatzinfo_1`, `zusatzinfo_2`, `zusatzinfo_3`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssdsiss", $lehrer_kuerzel, $datum, $stunde, $status, $diff, $vertritt_in, $zusatzinfo_1, $zusatzinfo_2, $zusatzinfo_3);

    // **SQL-Statement ausführen und prüfen**
    if ($stmt->execute()) {
        echo "✅ Zeile erfolgreich gespeichert.<br>";
        $imported_rows++;
    } else {
        echo "❌ SQL-Fehler: " . $stmt->error . "<br>";
    }
}

// **Datei schließen**
fclose($handle);

// **Ergebnis anzeigen**
echo "<h2>Import abgeschlossen</h2>";
echo "✅ $imported_rows Zeilen erfolgreich importiert.<br>";
echo "⚠ $skipped_rows Zeilen übersprungen (fehlerhafte Einträge).<br>";
echo "<br><a href='upload_and_filter.php'>⬅ Zurück zum Upload</a>";

// **Datenbankverbindung schließen**
$conn->close();
?>
