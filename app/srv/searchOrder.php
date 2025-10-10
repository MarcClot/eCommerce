<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Get the commandID trougth GET method
    $commandID = isset($_GET['commandID']) ? trim($_GET['commandID']) : '';
    
    if (empty($commandID)) {
        throw new Exception("Command ID is required");
    }
    
    $filePath = '/var/www/html/eCommerce/onlineOrders/onlineOrders.db';
    
    // Verify if the file exists
    if (!file_exists($filePath)) {
        throw new Exception("Orders database not found");
    }
    
    // Read binary file
    $binaryData = file_get_contents($filePath);
    if ($binaryData === false || strlen($binaryData) === 0) {
        throw new Exception("Unable to read orders database or database is empty");
    }
    
    // Unserialize data
    $orders = unserialize($binaryData);
    if (!is_array($orders)) {
        throw new Exception("Invalid data format in database");
    }
    
    // Search order by commandID
    $foundOrder = null;
    foreach ($orders as $order) {
        if ($order['commandID'] === $commandID) {
            $foundOrder = $order;
            break;
        }
    }
    
    if ($foundOrder === null) {
        throw new Exception("Order with ID '$commandID' not found");
    }
    
    echo json_encode([
        'success' => true,
        'order' => $foundOrder
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>