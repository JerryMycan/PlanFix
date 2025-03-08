<?php
// **Dateipfade definieren**
$input_file = "/var/www/html/planfix/vertretung.txt";  // Eingangsdatei
$output_file = "/var/www/html/planfix/vertretung.csv"; // Ausgabedatei

// Prüfen, ob die Eingabedatei existiert
if (!file_exists($input_file)) {
    die("❌ Fehler: Datei 'vertretung.txt' nicht gefunden.");
}

// **Dateiinhalt einlesen**
$file_content = file_get_contents($input_file);

// **Erkenne die echte Zeichenkodierung**
$encoding = mb_detect_encoding($file_content, ["UTF-8", "ISO-8859-1", "Windows-1252", "ASCII", "UTF-16", "UTF-32"], true);
if (!$encoding) {
    $encoding = "Windows-1252"; // Falls nichts erkannt wird, Standard setzen
}

// **Debugging: Zeige die erkannte Kodierung**
echo "<h2>📌 Erkannte Zeichenkodierung: <strong>$encoding</strong></h2>";

// **Falls nicht UTF-8, erzwingen wir die Konvertierung**
if ($encoding !== "UTF-8") {
    $file_content = mb_convert_encoding($file_content, "UTF-8", $encoding);
}

// **Manuelle Ersetzung problematischer Zeichen**
$replace_map = [
    "Š" => "ä", "š" => "ö", "Ž" => "ü", "ž" => "ß", 
    "Ÿ" => "Ä", "¥" => "Ö", "¤" => "Ü", "" => "ß"
];
$file_content = strtr($file_content, $replace_map);

// **Zeilenumbrüche normalisieren**
$file_content = str_replace(["\r\n", "\r"], "\n", $file_content);  // Windows/Mac-Zeilenumbrüche anpassen
$lines = preg_split("/\n+/", trim($file_content)); // Richtige Zeilenaufteilung

// **Maximale Anzahl an Spalten herausfinden**
$max_columns = 0;
$data_rows = [];

foreach ($lines as $line) {
    $columns = explode("\t", trim($line)); // Tab als Trennzeichen nutzen
    $max_columns = max($max_columns, count($columns));
    $data_rows[] = $columns; // Speichert alle Zeilen für später
}

// **Datei öffnen und in UTF-8 speichern**
$output_handle = fopen($output_file, "w");

// **BOM (Byte Order Mark) für Excel-Kompatibilität hinzufügen**
fwrite($output_handle, "\xEF\xBB\xBF"); // Damit Excel UTF-8 erkennt

// **Kopfzeile mit festgelegten und dynamischen Spalten erstellen**
$headers = ["Kürzel", "Datum", "Stunde", "Status", "Diff", "Vertritt_in"];

// Falls es mehr als 6 Spalten gibt, generiere dynamische Namen für die restlichen Spalten
for ($i = 7; $i <= $max_columns; $i++) {
    $headers[] = "Spalte_$i";  
}
fputcsv($output_handle, $headers, ";"); 

// **Alle Zeilen in CSV speichern**
foreach ($data_rows as $columns) {
    while (count($columns) < $max_columns) {
        $columns[] = "";  // Fehlende Spalten mit leerem Wert auffüllen
    }
    fputcsv($output_handle, $columns, ";");
}

// **Datei schließen**
fclose($output_handle);

echo "<h2>✅ Die Datei wurde erfolgreich in UTF-8 konvertiert und als CSV gespeichert.</h2>";
echo "<br><a href='/planfix/vertretung.csv' download>📥 CSV-Datei herunterladen</a>";
?>
