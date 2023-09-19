//Komplexe Übung PR3 Eugen Schmidt

<!DOCTYPE html>
<html>
<head>
    <title>Produktsuche</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<h1>Produktsuche</h1>

<form action="" method="post">
    Suche nach Name: <input type="text" name="search_name">
    <br>
    Preis kleiner als: <input type="text" name="max_price">
    <br>
    Preis groeßer als: <input type="text" name="min_price">
    <br>
    Kategorie: 
    <select name="category">
        <option value="">Alle</option>
        <option value="Speichermedien">Speichermedien</option>
        <option value="Fernseher">Fernseher</option>
        <option value="Monitore">Monitore</option>
        <option value="Kameras">Kameras</option>
    </select>
    <br>
    <input type="submit" value="Search/ Reset">
</form>

<?php

// Include "database" array
include 'database.php';

// Check if search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_name = $_POST['search_name'];
    $max_price = $_POST['max_price'];
    $min_price = $_POST['min_price'];
    $category = $_POST['category'];
    
    // Filter products based on search criteria
    $filtered_products = array_filter($products, function($product) use ($search_name, $max_price, $min_price, $category) {
        return (!$search_name || strpos(strtolower($product['name']), strtolower($search_name)) !== false) &&
               (!$max_price || $product['price'] <= $max_price) &&
               (!$min_price || $product['price'] >= $min_price) &&
               (!$category || $product['category'] == $category);
    });
} else {
    $filtered_products = $products;
}

// Display the products
foreach ($filtered_products as $product) {
    echo "<h2>" . $product['name'] . "</h2>";
    echo "<p>" . $product['description'] . "</p>";
    echo "<p>Preis: " . $product['price'] . "€</p>";
    echo "<p>Kategorie: " . $product['category'] . "</p>";
}

?>

</body>
</html>
