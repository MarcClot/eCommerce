function toggleQuantity() {
    //1st product
    if (firstCheckboxStretched.checked) {
        quantityFirstProduct.disabled = false;
    } else {
        quantityFirstProduct.disabled = true;
    }
    //2nd product
    if (secondCheckboxStretched.checked) {
        quantitySecondProduct.disabled = false;
    } else {
        quantitySecondProduct.disabled = true;
    }
    // 3rd product
    if (thirdCheckboxStretched.checked) {
        quantityThirdProduct.disabled = false;
    } else {
        quantityThirdProduct.disabled = true;
    }
    // 4th product
    if (fourthCheckboxStretched.checked) {
        quantityFourthProduct.disabled = false;
    } else {
        quantityFourthProduct.disabled = true;
    }
}

function sendInfo(){
    const result = document.getElementById("result");
    
    // Collect all form data
    const commandID = document.getElementById("inputCommandID").value;
    const name = document.getElementById("Name").value;
    const lastName = document.getElementById("LastName").value;
    const secondLastName = document.getElementById("SecondLastName").value;
    const email = document.getElementById("inputEmail").value;
    const phone = document.getElementById("inputPhone").value;
    const address = document.getElementById("inputAddress").value;
    
    // Prepare product data
    const products = [];
    
    if (document.getElementById('firstCheckboxStretched').checked) {
        products.push({
            name: 'Bicycle carrier',
            price: 80,
            quantity: parseInt(document.getElementById('quantityFirstProduct').value)
        });
    }
    
    if (document.getElementById('secondCheckboxStretched').checked) {
        products.push({
            name: 'Snow chains',
            price: 60,
            quantity: parseInt(document.getElementById('quantitySecondProduct').value)
        });
    }
    
    if (document.getElementById('thirdCheckboxStretched').checked) {
        products.push({
            name: 'GPS',
            price: 120,
            quantity: parseInt(document.getElementById('quantityThirdProduct').value)
        });
    }
    
    if (document.getElementById('fourthCheckboxStretched').checked) {
        products.push({
            name: 'Spare tire',
            price: 130,
            quantity: parseInt(document.getElementById('quantityFourthProduct').value)
        });
    }
    
    // Create FormData with all data
    const formData = new FormData();
    formData.append('commandID', commandID);
    formData.append('name', name);
    formData.append('lastName', lastName);
    formData.append('secondLastName', secondLastName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('address', address);
    formData.append('products', JSON.stringify(products));
    
    // Build the request
    const ip = "192.168.1.86";
    const folder = "eCommerce/app/srv";
    const phpFile = "createOrder.php";
    const request = "http://" + ip + "/" + folder + "/" + phpFile;
    
    // Send the request
    fetch(request, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById("result").textContent = "Order created successfully. Total price with IVA (21%): " + result.priceWithIVA.toFixed(2) + "€";
        } else {
            document.getElementById("result").textContent = "Error: " + result.error;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById("result").textContent = "Error processing the order";
    });
}