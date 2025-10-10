<?php

function readOrdersFromBinaryFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    $binaryData = file_get_contents($filePath);
    if ($binaryData === false || strlen($binaryData) === 0) {
        return [];
    }
    
    $orders = unserialize($binaryData);
    if (!is_array($orders)) {
        return [];
    }
    
    return $orders;
}

function buildFullName($name, $lastName, $secondLastName) {
    return trim($name . ' ' . $lastName . ' ' . $secondLastName);
}

function formatProductsList($products) {
    if (!is_array($products) || empty($products)) {
        return 'No products';
    }
    
    $productsList = '';
    foreach ($products as $product) {
        $productsList .= $product['name'] . ' (x' . $product['quantity'] . '), ';
    }
    return rtrim($productsList, ', ');
}

// Calculate total quantity of products in an order
function getTotalQuantity($products) {
    if (!is_array($products)) {
        return 0;
    }
    
    $total = 0;
    foreach ($products as $product) {
        $total += $product['quantity'];
    }
    return $total;
}

//Build multidimensional associative array from raw order data
function buildOrdersArray($ordersRaw) {
    $orders = [];
    
    foreach ($ordersRaw as $order) {
        $products = $order['products'];
        
        $orders[] = [
            'orderInfo' => [
                'commandID' => $order['commandID'],
                'createdAt' => $order['createdAt'],
                'formattedDate' => formatDate($order['createdAt'])
            ],
            'customerInfo' => [
                'name' => $order['name'],
                'lastName' => $order['lastName'],
                'secondLastName' => $order['secondLastName'],
                'fullName' => buildFullName($order['name'], $order['lastName'], $order['secondLastName']),
                'email' => $order['email'],
                'phone' => $order['phone'],
                'address' => $order['address']
            ],
            'products' => $products,
            'productsSummary' => [
                'list' => formatProductsList($products),
                'totalItems' => getTotalQuantity($products),
                'totalProducts' => is_array($products) ? count($products) : 0
            ],
            'pricing' => [
                'priceWithoutIVA' => $order['priceWithoutIVA'],
                'priceWithIVA' => $order['priceWithIVA'],
                'IVAAmount' => $order['priceWithIVA'] - $order['priceWithoutIVA'],
                'IVAPercentage' => 21
            ]
        ];
    }
    
    return $orders;
}

// Format date to readable format
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Format price with currency symbol
function formatPrice($price) {
    return number_format($price, 2) . '€';
}

//Calculate total revenue from all orders
function calculateTotalRevenue($orders) {
    $total = 0;
    foreach ($orders as $order) {
        $total += $order['pricing']['priceWithIVA'];
    }
    return $total;
}

// Calculate total IVA collected from all orders
function calculateTotalIVA($orders) {
    $total = 0;
    foreach ($orders as $order) {
        $total += $order['pricing']['IVAAmount'];
    }
    return $total;
}

// Get statistics from orders
function getOrderStatistics($orders) {
    return [
        'totalOrders' => count($orders),
        'totalRevenue' => calculateTotalRevenue($orders),
        'totalIVA' => calculateTotalIVA($orders),
        'averageOrderValue' => count($orders) > 0 ? calculateTotalRevenue($orders) / count($orders) : 0
    ];
}

//Sort orders by date (newest first)
function sortOrdersByDate($orders) {
    usort($orders, function($a, $b) {
        return strtotime($b['createdAt']) - strtotime($a['createdAt']);
    });
    return $orders;
}

// ========== MAIN CODE ==========

try {
    // File path
    $filePath = '/var/www/html/eCommerce/onlineOrders/onlineOrders.db';
    
    // Read orders from binary file
    $ordersRaw = readOrdersFromBinaryFile($filePath);
    
    // Sort orders by date
    $ordersRaw = sortOrdersByDate($ordersRaw);
    
    // Build multidimensional array
    $orders = buildOrdersArray($ordersRaw);
    
    // Get statistics
    $stats = getOrderStatistics($orders);
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $orders = [];
    $stats = ['totalOrders' => 0, 'totalRevenue' => 0, 'totalIVA' => 0, 'averageOrderValue' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Orders</title>
    <link rel="icon" type="image/x-icon" href="../cli/media/faviconlogo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h4 class="display-5 text-center">All Orders</h4><br>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($orders)): ?>
            <div class="alert alert-info" role="alert">
                No orders found in the database.
            </div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text display-6"><?php echo $stats['totalOrders']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <p class="card-text display-6"><?php echo formatPrice($stats['totalRevenue']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">Total IVA Collected</h5>
                            <p class="card-text display-6"><?php echo formatPrice($stats['totalIVA']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Average Order</h5>
                            <p class="card-text display-6"><?php echo formatPrice($stats['averageOrderValue']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Items</th>
                            <th>Price without IVA</th>
                            <th>IVA Amount</th>
                            <th>Price with IVA</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['orderInfo']['commandID']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['customerInfo']['fullName']); ?></td>
                                <td><?php echo htmlspecialchars($order['customerInfo']['email']); ?></td>
                                <td><?php echo htmlspecialchars($order['customerInfo']['phone']); ?></td>
                                <td><?php echo htmlspecialchars($order['customerInfo']['address']); ?></td>
                                <td><?php echo htmlspecialchars($order['productsSummary']['list']); ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo $order['productsSummary']['totalItems']; ?> items 
                                    </span>
                                </td>
                                <td><?php echo formatPrice($order['pricing']['priceWithoutIVA']); ?></td>
                                <td class="text-warning"><?php echo formatPrice($order['pricing']['IVAAmount']); ?></td>
                                <td><strong class="text-success"><?php echo formatPrice($order['pricing']['priceWithIVA']); ?></strong></td>
                                <td><?php echo $order['orderInfo']['formattedDate']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="d-grid gap-2 col-6 mx-auto mt-4">
            <button type="button" onclick="window.location = '../cli/operation.html';" class="btn btn-light">
                Back to Menu
            </button>
            <button type="button" onclick="window.location = '../cli/index.html';" class="btn btn-info">
                Back to Home
            </button>
        </div>
    </div>
</body>
</html>