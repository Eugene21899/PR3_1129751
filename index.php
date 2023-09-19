// Eugen Schmidt XX00-PR3-SU1 1129751 19.09.2023 

<?php
// Metadata and database configuration


$host = 'localhost';
$db   = 'Webshop_PR3';
$user = 'root'; 
$pass = ''; 

// Database connection
$mysqli = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Load categories from the database
$kategorien = [];
$query = "SELECT DISTINCT kategorie FROM Produkte";
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) {
    $kategorien[] = $row['kategorie'];
}

// Initialize results array
$results = [];
$whereClause = [];
$params = [];
$types = "";

// Handle forms
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle text search
    if (isset($_POST['suchtext']) && !empty($_POST['suchtext'])) {
        $suchtext = "%" . $mysqli->real_escape_string($_POST['suchtext']) . "%";
        $whereClause[] = "name LIKE ?";
        $params[] = $suchtext;
        $types .= "s";
    }

    // Handle category selection
    if (isset($_POST['kategorie']) && !empty($_POST['kategorie'])) {
        $kategorie = $mysqli->real_escape_string($_POST['kategorie']);
        $whereClause[] = "kategorie = ?";
        $params[] = $kategorie;
        $types .= "s";
    }

    // Construct the query
    $query = "SELECT name, preis FROM Produkte";
    if (!empty($whereClause)) {
        $query .= " WHERE " . implode(" AND ", $whereClause);
    }

    // Execute the query and fetch results
    $stmt = $mysqli->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produktsuche</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Produktsuche</h1>
    <!-- Search form -->
    <form action="index.php" method="post">
        <label for="suchtext">Produkt suchen:</label>
        <input type="text" id="suchtext" name="suchtext" placeholder="Produkt suchen...">
        <label for="kategorie">Kategorie:</label>
        <select id="kategorie" name="kategorie">
            <option value="">Alle Kategorien</option>
            <?php
            // Populate dropdown with categories from the database
            foreach ($kategorien as $kategorie) {
                echo "<option value='$kategorie'>$kategorie</option>";
            }
            ?>
        </select>
        <input type="submit" value="Suchen/ Reset">
    </form>

    <!-- Display the number of products found -->
    <h2><?php echo count($results) . " Produkte gefunden"; ?></h2>

    <!-- Display search results in a table -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Preis</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are results to display
            if (count($results) > 0) {
                // Loop through each result and display in a table row
                foreach ($results as $row) {
                    echo "<tr><td>" . $row["name"] . "</td><td>" . $row["preis"] . "€</td></tr>";
                }
            } else {
                // Display a message if no results are found
                echo "<tr><td colspan='2'>Keine Produkte gefunden.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
