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