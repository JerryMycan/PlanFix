<?php
// Verbindung zur MySQL-Datenbank herstellen
$servername = "localhost";
$username = "root"; 
$password = "Kym7HEbeS6#"; 
$dbname = "c1_planfix"; 

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->query("USE c1_planfix;");

// Verbindung prüfen
if ($conn->connect_error) {
    die("❌ Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Definiere die möglichen SQL-Abfragen
$queries = [
    "alle" => "SELECT * FROM vertretungen",
    "vertritt" => "SELECT * FROM vertretungen WHERE status = 'vertritt'",
    "ausgefallen" => "SELECT * FROM vertretungen WHERE status = 'ausgefallen'",
    "sort_datum" => "SELECT * FROM vertretungen ORDER BY datum ASC",
    "neueste_10" => "SELECT * FROM vertretungen ORDER BY datum DESC LIMIT 10"
];

// Prüfe, ob eine Abfrage ausgeführt werden soll
$query_key = isset($_GET['query']) ? $_GET['query'] : null;
$sql = $query_key && isset($queries[$query_key]) ? $queries[$query_key] : null;
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>SQL Selects</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { width: 80%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
        a { display: inline-block; padding: 10px 15px; margin: 5px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        a:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h1>SQL Abfragen - Vertretungen</h1>
    
    <h3>Wähle eine Abfrage:</h3>
    <a href="?query=alle">🔍 Alle Vertretungen</a>
    <a href="?query=vertritt">✅ Vertretungen anzeigen</a>
    <a href="?query=ausgefallen">❌ Ausgefallene Stunden</a>
    <a href="?query=sort_datum">📅 Nach Datum sortiert</a>
    <a href="?query=neueste_10">🔥 Neueste 10 Einträge</a>

    <?php if ($sql): ?>
        <h2>Ergebnisse</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Lehrer</th>
                <th>Datum</th>
                <th>Stunde</th>
                <th>Status</th>
                <th>Diff</th>
                <th>Vertritt in</th>
                <th>Zusatzinfo 1</th>
                <th>Zusatzinfo 2</th>
                <th>Zusatzinfo 3</th>
            </tr>

            <?php
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['lehrer_kuerzel']}</td>
                        <td>{$row['datum']}</td>
                        <td>{$row['stunde']}</td>
                        <td>{$row['status']}</td>
                        <td>{$row['diff']}</td>
                        <td>{$row['vertritt_in']}</td>
                        <td>{$row['zusatzinfo_1']}</td>
                        <td>{$row['zusatzinfo_2']}</td>
                        <td>{$row['zusatzinfo_3']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>⚠ Keine Ergebnisse gefunden.</td></tr>";
            }
            ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

<?php
// Verbindung schließen
$conn->close();
?>
