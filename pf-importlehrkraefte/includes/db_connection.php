<?php
// Lade die zentral definierten Datenbank-Konstanten aus db_config.php
require_once 'db_config.php';

// Überprüfe, ob das Skript lokal (z. B. auf dem Entwicklungsrechner) läuft
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    // Lokale Umgebung (z. B. XAMPP, Entwicklungsserver)
    $host = DB_LOCAL_HOST;
    $user = DB_LOCAL_USER;
    $pass = DB_LOCAL_PASSWORD;
    $db   = DB_LOCAL_NAME;
} else {
    // Serverumgebung (z. B. Produktivsystem, Hosting)
    $host = DB_SERVER_HOST;
    $user = DB_SERVER_USER;
    $pass = DB_SERVER_PASSWORD;
    $db   = DB_SERVER_NAME;
}

// Zeichensatz festlegen (UTF-8 mit Unterstützung für Sonderzeichen)
$charset = 'utf8mb4';

// Erzeuge die Datenbankverbindungs-DSN-Zeichenkette
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Optionen für PDO: Fehlerbehandlung und Verhalten definieren
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // bei Fehlern Exception werfen
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Abfragen liefern assoziative Arrays
    PDO::ATTR_EMULATE_PREPARES   => false                    // echte Prepared Statements vom MySQL-Server
];

// Versuche, eine neue PDO-Verbindung aufzubauen
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Wenn ein Fehler auftritt: Skript stoppen und Fehlermeldung anzeigen
    die('Verbindungsfehler: ' . $e->getMessage());
}
?>
