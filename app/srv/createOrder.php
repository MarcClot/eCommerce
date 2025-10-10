<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

try {
    // Collect form data
    $commandID = isset($_POST['commandID']) ? $_POST['commandID'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
    $secondLastName = isset($_POST['secondLastName']) ? $_POST['secondLastName'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $products = isset($_POST['products']) ? json_decode($_POST['products'], true) : [];
    
    // Calculate total price without IVA
    $priceWithoutIVA = 0;
    foreach ($products as $product) {
        $priceWithoutIVA += $product['price'] * $product['quantity'];
    }
    
    // Calculate price with IVA (21%)
    $priceWithIVA = $priceWithoutIVA * 1.21;
    
    // Connect to SQLite database
    $dbPath = '/var/www/html/eCommerce/onlineOrders/onlineOrders.db';
    $db = new PDO("sqlite:$dbPath");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        commandID TEXT NOT NULL,
        name TEXT NOT NULL,
        lastName TEXT NOT NULL,
        secondLastName TEXT,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NOT NULL,
        products TEXT NOT NULL,
        priceWithoutIVA REAL NOT NULL,
        priceWithIVA REAL NOT NULL,
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Full name in one variable
    $fullName = trim($name . ' ' . $lastName . ' ' . $secondLastName);
    
    // Insert order into database
    $stmt = $db->prepare("INSERT INTO orders 
        (commandID, name, lastName, secondLastName, email, phone, address, products, priceWithoutIVA, priceWithIVA) 
        VALUES 
        (:commandID, :name, :lastName, :secondLastName, :email, :phone, :address, :products, :priceWithoutIVA, :priceWithIVA)");
    
    $stmt->execute([
        ':commandID' => $commandID,
        ':name' => $name,
        ':lastName' => $lastName,
        ':secondLastName' => $secondLastName,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address,
        ':products' => json_encode($products),
        ':priceWithoutIVA' => $priceWithoutIVA,
        ':priceWithIVA' => $priceWithIVA
    ]);
    
    // Return only the price with IVA
    echo json_encode([
        'success' => true,
        'priceWithIVA' => $priceWithIVA
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>