<?php
session_start(); // Für Rückmeldungen an index.php

// Datenbankverbindung und PhpSpreadsheet einbinden
require_once '../includes/db_connection.php';
require_once '../vendor/autoload.php'; // PhpSpreadsheet wird hier automatisch geladen

use PhpOffice\PhpSpreadsheet\IOFactory;

// Hilfsfunktion zur Korrektur fehlerhafter Umlaute aus Windows-1252
function fix_umlaute($text) {
    return strtr($text, [
        'Š' => 'ä', 'š' => 'ö', 'Ÿ' => 'Ü', 'ˆ' => 'ü',
        'Ž' => 'ö', 'ž' => 'ß', '‚' => 'ä', 'ƒ' => 'ö',
        '„' => 'ö', '†' => 'ö', '‡' => 'ü', '‰' => 'ß',
        '‹' => 'ü', 'Œ' => 'ö'
    ]);
}

// Fehlerprüfung: Datei wurde nicht hochgeladen oder fehlerhaft
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['feedback'] = "❌ Datei konnte nicht hochgeladen werden.";
    header("Location: ../index.php");
    exit;
}

// Dateiendung prüfen
$allowed = ['csv', 'xlsx'];
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

if (!in_array($ext, $allowed)) {
    $_SESSION['feedback'] = "❌ Ungültiges Dateiformat. Nur .csv und .xlsx erlaubt.";
    header("Location: ../index.php");
    exit;
}

// Temporären Pfad der hochgeladenen Datei speichern
$tmpPath = $_FILES['file']['tmp_name'];
$rows = []; // Hier werden die importierten Zeilen gespeichert

// Verarbeitung: CSV-Datei
if ($ext === 'csv') {
    $handle = fopen($tmpPath, "r");
    $header = fgetcsv($handle, 1000, ";"); // Kopfzeile
    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        $rows[] = array_combine($header, $data); // assoziatives Array pro Zeile
    }
    fclose($handle);
} else {
    // Verarbeitung: XLSX-Datei (Excel)
    $spreadsheet = IOFactory::load($tmpPath); // Lade Excel-Datei
    $sheet = $spreadsheet->getActiveSheet();  // Aktives Tabellenblatt
    $rows = $sheet->toArray(null, true, true, true); // Keys = Spaltenbuchstaben A, B, C ...

    // Kopfzeile entnehmen
    $header = array_shift($rows);
    $converted = [];

    // Alle Zeilen mit Spaltennamen als Schlüssel mappen
    foreach ($rows as $row) {
        $assoc = [];
        foreach ($header as $colKey => $title) {
            $assoc[$title] = $row[$colKey];
        }
        $converted[] = $assoc;
    }
    $rows = $converted;
}

// Datenbankverbindung (aus db_connection.php → $pdo)
$inserted = 0; // Zähler für importierte Zeilen

foreach ($rows as $r) {
    // Pflichtfeld Kürzel prüfen
    $kuerzel = strtoupper(trim($r['Lehrer_Kuerzel'] ?? ''));
    if (!$kuerzel) continue;

    // SQL: Insert oder Update bei vorhandenem Kürzel (ON DUPLICATE KEY)
    $sql = "INSERT INTO lehrkraefte 
        (kuerzel, vorname, nachname, lehrbefaehigung, unterrichtsfaecher, email_dienst, mobil)
        VALUES (:k, :v, :n, :lb, :uf, :em, :m)
        ON DUPLICATE KEY UPDATE 
            vorname = VALUES(vorname), 
            nachname = VALUES(nachname),
            lehrbefaehigung = VALUES(lehrbefaehigung),
            unterrichtsfaecher = VALUES(unterrichtsfaecher),
            email_dienst = VALUES(email_dienst),
            mobil = VALUES(mobil)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':k' => $kuerzel,
        ':v' => fix_umlaute(trim($r['Vorname'] ?? '')),
        ':n' => fix_umlaute(trim($r['Nachname'] ?? '')),
        ':lb' => trim($r['Lehrbefaehigung'] ?? ''),
        ':uf' => trim($r['Lehrer_Unterrichtsfaecher_Schule'] ?? ''),
        ':em' => trim($r['Email_dienstlich'] ?? ''),
        ':m' => trim($r['Mobil'] ?? ''),
    ]);

    $inserted++;
}

// Rückmeldung speichern & zur Hauptseite zurückleiten
$_SESSION['feedback'] = "✅ $inserted Einträge erfolgreich importiert.";
header("Location: ../index.php");
