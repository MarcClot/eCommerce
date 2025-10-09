function toggleQuantity() {

    //1s product 
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

function sendInfo() {
    const values = new FormData(document.getElementById("FormCreateOrder"));
    const result = document.getElementById("result");

    comandID = values.get("inputCommandID");
    username = values.get("Name");
    userLastName = values.get("LastName");
    user2nLastName = values.get("SecondLastName");
    email = values.get("inputEmail");
    phone = values.get("inputPhone");
    address = values.get("inputAddress");
    priceFirstProduct = values.get("priceFirstProduct");
    priceSecondProduct = values.get("priceSecondProduct");
    priceThirdProduct = values.get("priceThirdProduct");
    priceFourthProduct = values.get("priceFourthProduct");

    const ip = "localhost"; //for the moment we're going to leave it in localhost
    const folder = "../srv"; 
    const PHPfile = "createOrder.php"; 
    const request = "http://" + ip + "/" + folder + "/" + PHPfile + 
                   "?inputCommandID=" + encodeURIComponent(comandID) + 
                   "&Name=" + encodeURIComponent(username) + 
                   "&LastName=" + encodeURIComponent(userLastName) + 
                   "&SecondLastName=" + encodeURIComponent(user2nLastName) + 
                   "&inputEmail=" + encodeURIComponent(email) + 
                   "&inputPhone=" + encodeURIComponent(phone) + 
                   "&inputAddress=" + encodeURIComponent(address) + 
                   "&priceFirstProduct=" + encodeURIComponent(priceFirstProduct) + 
                   "&priceSecondProduct=" + encodeURIComponent(priceSecondProduct) + 
                   "&priceThirdProduct=" + encodeURIComponent(priceThirdProduct) + 
                   "&priceFourthProduct=" + encodeURIComponent(priceFourthProduct);

     fetch(request, {
        method: 'GET'
    })
        .then(reply => reply.json()) // JSON
        .then(outcome => {
        if (outcome.totalPriceWithIVA) {
            //If server has given the price with IVA. we show the result 
            result.textContent = `Order submitted successfully! The total price with IVA is: €${outcome.totalPriceWithIVA.toFixed(2)}`;
        } else {
            result.textContent = "Error: No price with IVA returned from server.";
        }
    }) 
        .catch(errors => { 						
            result.textContent = "Error sending data to server";
        });
}


