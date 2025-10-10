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
    
    // Get current timestamp
    $createdAt = date('Y-m-d H:i:s');
    
    // Build order array
    $order = [
        'commandID' => $commandID,
        'name' => $name,
        'lastName' => $lastName,
        'secondLastName' => $secondLastName,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'products' => $products,
        'priceWithoutIVA' => $priceWithoutIVA,
        'priceWithIVA' => $priceWithIVA,
        'createdAt' => $createdAt
    ];
    
    // Path to binary file
    $filePath = '/var/www/html/eCommerce/onlineOrders/onlineOrders.db';
    
    // Read existing orders from binary file
    $orders = [];
    if (file_exists($filePath)) {
        $binaryData = file_get_contents($filePath);
        if ($binaryData !== false && strlen($binaryData) > 0) {
            $orders = unserialize($binaryData);
            if (!is_array($orders)) {
                $orders = [];
            }
        }
    }
    
    // Add new order to array
    $orders[] = $order;
    
    // Serialize and save to binary file
    $binaryData = serialize($orders);
    $result = file_put_contents($filePath, $binaryData);
    
    if ($result === false) {
        throw new Exception("Failed to write to file");
    }
    
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