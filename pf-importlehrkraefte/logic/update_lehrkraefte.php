<?php
// ======================================================
// Update-Skript für Lehrkräfte
// ======================================================

// Datenbankverbindung einbinden
require_once '../includes/db_connection.php';

// Wichtige Laufzeitkonfiguration für große Formulare
ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');
ini_set('max_input_vars', '5000');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Prüfung, ob Daten korrekt empfangen wurden
if (!isset($_POST['data']) || !is_array($_POST['data'])) {
    die("Keine gültigen Daten empfangen.");
}

// SQL-Statement zum Aktualisieren vorbereiten
$sql = "UPDATE lehrkraefte SET 
            vorname = :vorname,
            nachname = :nachname,
            lehrbefaehigung = :lehrbefaehigung,
            unterrichtsfaecher = :unterrichtsfaecher,
            email_dienst = :email_dienst,
            mobil = :mobil,
            unterrichtswirksame_stunden = :uws,
            max_vertretung = :max_vertretung,
            vertretungsreserve = :reserve,
            aktiv = :aktiv,
            bemerkung = :bemerkung
        WHERE kuerzel = :kuerzel";

$stmt = $pdo->prepare($sql);

// Alle Einträge verarbeiten
foreach ($_POST['data'] as $i => $eintrag) {

    // Parameter vorbereiten
    $params = [
        ':vorname'        => $eintrag['vorname'] ?? '',
        ':nachname'       => $eintrag['nachname'] ?? '',
        ':lehrbefaehigung'=> $eintrag['lehrbefaehigung'] ?? '',
        ':unterrichtsfaecher' => $eintrag['unterrichtsfaecher'] ?? '',
        ':email_dienst'   => $eintrag['email_dienst'] ?? '',
        ':mobil'          => $eintrag['mobil'] ?? '',
        ':uws' => isset($eintrag['unterrichtswirksame_stunden']) && is_numeric($eintrag['unterrichtswirksame_stunden'])
            ? (float)$eintrag['unterrichtswirksame_stunden']
            : null,
        ':max_vertretung' => isset($eintrag['max_vertretung']) && is_numeric($eintrag['max_vertretung'])
            ? (float)$eintrag['max_vertretung']
            : null,
        ':reserve'        => isset($eintrag['vertretungsreserve']) ? (int)$eintrag['vertretungsreserve'] : 0,
        ':aktiv'          => isset($eintrag['aktiv']) ? (int)$eintrag['aktiv'] : 1,
        ':bemerkung'      => $eintrag['bemerkung'] ?? '',
        ':kuerzel'        => $eintrag['kuerzel'] ?? ''
    ];

    // Kürzel darf nicht leer sein
    if (trim($params[':kuerzel']) === '') {
        continue;
    }

    // Eintrag aktualisieren
    try {
        $stmt->execute($params);
        // (Optional) Logging hier möglich
    } catch (PDOException $e) {
        // Logging oder Fehlerhandling bei Einzelfehler
        error_log("Fehler bei Eintrag $i ({$params[':kuerzel']}): " . $e->getMessage());
    }
}

// Nach erfolgreicher Verarbeitung zurück zur Bearbeitungsseite
header("Location: ../lehrkraefte_edit.php?success=1");
exit;
