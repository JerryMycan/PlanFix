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

// Definiere SQL-Abfragen
$queries = [
    "alle" => "SELECT * FROM vertretungen",
    "vertritt" => "SELECT * FROM vertretungen WHERE status = 'vertritt'",
    "ausgefallen" => "SELECT * FROM vertretungen WHERE status = 'ausgefallen'",
    "sort_datum" => "SELECT * FROM vertretungen ORDER BY datum ASC",
    "neueste_10" => "SELECT * FROM vertretungen ORDER BY datum DESC LIMIT 10"
];

// Prüfe, ob eine Abfrage ausgeführt werden soll
$query_key = isset($_GET['query']) ? $_GET['query'] : "alle"; // Standard: Alle Vertretungen
$sql = $queries[$query_key];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>SQL Selects mit Live-Suche</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { width: 90%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #007bff; color: white; }
        a { display: inline-block; padding: 10px 15px; margin: 5px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        a:hover { background-color: #0056b3; }
        input { width: 100%; padding: 5px; }
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
        <table id="vertretungstabelle">
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

            <!-- 🟢 Eingabefelder für die Live-Suche -->
            <tr>
                <td></td> <!-- ID bleibt leer -->
                <td><input type="text" class="filter" data-column="1" placeholder="🔍 Lehrer"></td>
                <td><input type="text" class="filter" data-column="2" placeholder="🔍 Datum"></td>
                <td><input type="text" class="filter" data-column="3" placeholder="🔍 Stunde"></td>
                <td><input type="text" class="filter" data-column="4" placeholder="🔍 Status"></td>
                <td><input type="text" class="filter" data-column="5" placeholder="🔍 Diff"></td>
                <td><input type="text" class="filter" data-column="6" placeholder="🔍 Vertritt in"></td>
                <td><input type="text" class="filter" data-column="7" placeholder="🔍 Zusatzinfo 1"></td>
                <td><input type="text" class="filter" data-column="8" placeholder="🔍 Zusatzinfo 2"></td>
                <td><input type="text" class="filter" data-column="9" placeholder="🔍 Zusatzinfo 3"></td>
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

<!-- 🟢 JavaScript für die verbesserte Live-Suche -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const filters = document.querySelectorAll(".filter");
    filters.forEach(filter => {
        filter.addEventListener("keyup", function () {
            filterTable();
        });
    });
});

function filterTable() {
    var table, tr, i, j, allMatch;
    table = document.getElementById("vertretungstabelle");
    tr = table.getElementsByTagName("tr");

    for (i = 2; i < tr.length; i++) {
        allMatch = true;
        for (j = 1; j <= 9; j++) {  // Wir haben 9 filterbare Spalten
            var input = document.querySelector(".filter[data-column='" + j + "']");
            var td = tr[i].getElementsByTagName("td")[j];
            if (input && td) {
                var filter = input.value.toUpperCase();
                var txtValue = td.textContent || td.innerText;
                if (filter && txtValue.toUpperCase().indexOf(filter) === -1) {
                    allMatch = false;
                    break;
                }
            }
        }
        tr[i].style.display = allMatch ? "" : "none";
    }
}
</script>

</body>
</html>

<?php
// Verbindung schließen
$conn->close();
?>
