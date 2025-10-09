<?php
	header('Content-Type: application/json');

    // Calculate 21% IVA
    $priceFirstProduct = isset($_GET['priceFirstProduct']) ? (float)$_GET['priceFirstProduct'] : 0;
    $priceSecondProduct = isset($_GET['priceSecondProduct']) ? (float)$_GET['priceSecondProduct'] : 0;
    $priceThirdProduct = isset($_GET['priceThirdProduct']) ? (float)$_GET['priceThirdProduct'] : 0;
    $priceFourthProduct = isset($_GET['priceFourthProduct']) ? (float)$_GET['priceFourthProduct'] : 0;

	 $totalBase = $priceFirstProduct + $priceSecondProduct + $priceThirdProduct + $priceFourthProduct;

     function CalculatePriceWithIVA($basePrice) {
        $iva = 21;  
        return $basePrice + ($basePrice * $iva / 100);

         $totalWithVA = CalculatePriceWithIVA($totalBase);
    }

    // Display interface
    $comandID = isset($_GET['inputCommandID']) ? $_GET['inputCommandID'] : '';
    $username = isset($_GET['Name']) ? $_GET['Name'] : '';
    $userLastName = isset($_GET['LastName']) ? $_GET['LastName'] : '';
    $user2nLastName = isset($_GET['SecondLastName']) ? $_GET['SecondLastName'] : '';
    $email = isset($_GET['inputEmail']) ? $_GET['inputEmail'] : '';
    $phone = isset($_GET['inputPhone']) ? $_GET['inputPhone'] : '';
    $address = isset($_GET['inputAddress']) ? $_GET['inputAddress'] : '';

    try {
        $pdo = new PDO('sqlite:/var/www/html/eCommerce/onlineOrders/onlineOrders.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            orderID TEXT,
            firstName TEXT,
            lastName TEXT,
            secondLastName TEXT,
            email TEXT,
            phone TEXT,
            address TEXT,
            priceFirstProduct REAL,
            priceSecondProduct REAL,
            priceThirdProduct REAL,
            priceFourthProduct REAL,
            totalBasePrice REAL,
            totalPriceWithIVA REAL,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $pdo->prepare("INSERT INTO orders (orderID, firstName, lastName, secondLastName, email, phone, address, priceFirstProduct, priceSecondProduct, priceThirdProduct, priceFourthProduct, totalBasePrice, totalPriceWithIVA) 
                               VALUES (:orderID, :firstName, :lastName, :secondLastName, :email, :phone, :address, :priceFirstProduct, :priceSecondProduct, :priceThirdProduct, :priceFourthProduct, :totalBasePrice, :totalPriceWithIVA)");

        $stmt->bindParam(':orderID', $orderID);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':secondLastName', $secondLastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':priceFirstProduct', $priceFirstProduct);
        $stmt->bindParam(':priceSecondProduct', $priceSecondProduct);
        $stmt->bindParam(':priceThirdProduct', $priceThirdProduct);
        $stmt->bindParam(':priceFourthProduct', $priceFourthProduct);
        $stmt->bindParam(':totalBasePrice', $totalBasePrice);
        $stmt->bindParam(':totalPriceWithIVA', $totalPriceWithIVA);

        $stmt->execute();

    } catch (PDOException $error) {
        echo json_encode(['error' => 'Error saving the order: ' . $error->getMessage()]);
        exit();
    }

    echo json_encode([
        'orderID' => $orderID,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'secondLastName' => $secondLastName,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'priceFirstProduct' => $priceFirstProduct,
        'priceSecondProduct' => $priceSecondProduct,
        'priceThirdProduct' => $priceThirdProduct,
        'priceFourthProduct' => $priceFourthProduct,
        'totalBasePrice' => $totalBasePrice,
        'totalPriceWithIVA' => $totalPriceWithIVA,
        'message' => 'Order saved successfully'
    ]);
?>