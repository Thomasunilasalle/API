<?php
// Connexion à la base de données
$user = 'root';
$password = 'root';
$db = 'hearthstone_api_db';
$host = 'localhost';
$port = 3306;

$conn = new mysqli($host, $user, $password, $db, $port);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Récupération des informations depuis la base de données
$sql = "SELECT * FROM user_filters ORDER BY timestamp DESC";
$result = $conn->query($sql);

// Affichage des informations dans une table HTML avec une classe CSS pour le style
echo "<table border='1' class='white-text-table'>";
echo "<tr><th>ID</th><th>Timestamp</th><th>Set</th><th>Class</th><th>Mana Cost</th><th>Attack</th><th>Health</th><th>Rarity</th><th>Minion Type</th><th>Spell School</th><th>Text Filter</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['timestamp'] . "</td>";
    echo "<td>" . $row['set_filter'] . "</td>";
    echo "<td>" . $row['class_filter'] . "</td>";
    echo "<td>" . $row['mana_cost_filter'] . "</td>";
    echo "<td>" . $row['attack_filter'] . "</td>";
    echo "<td>" . $row['health_filter'] . "</td>";
    echo "<td>" . $row['rarity_filter'] . "</td>";
    echo "<td>" . $row['minion_type_filter'] . "</td>";
    echo "<td>" . $row['spell_school_filter'] . "</td>";
    echo "<td>" . $row['text_filter'] . "</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();

?>
