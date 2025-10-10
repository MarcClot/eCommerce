
function showError(message) {
    const resultContainer = document.getElementById("resultContainer");
    resultContainer.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <strong>Error:</strong> ${message}
        </div>
    `;
}

function showInfo(message) {
    const resultContainer = document.getElementById("resultContainer");
    resultContainer.innerHTML = `
        <div class="alert alert-info" role="alert">
            ${message}
        </div>
    `;
}

function searchOrder() {
    const commandID = document.getElementById("inputCommandID").value.trim();
    
    if (!commandID) {
        showError("Please enter a Command ID");
        return;
    }

    // buildRequest() from reusable.js
    const request = buildRequest("searchOrder.php") + "?commandID=" + encodeURIComponent(commandID);
    
    fetch(request, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            displayOrderDetails(result.order);
        } else {
            showError(result.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError("Error connecting to the server");
    });
}

function displayOrderDetails(order) {
    const resultContainer = document.getElementById("resultContainer");
    
    // Build the products list
    let productsList = '';
    order.products.forEach(product => {
        // Using formatPrice()
        productsList += `
            <tr>
                <td>${product.name}</td>
                <td>${formatPrice(product.price)}</td>
                <td>${product.quantity}</td>
                <td>${formatPrice(product.price * product.quantity)}</td>
            </tr>
        `;
    });

    // Using reusable buildFullName() from reusable.js
    resultContainer.innerHTML = `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>Order Details - ID: ${order.commandID}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Customer Information</h6>
                        <p><strong>Full Name:</strong> ${buildFullName(order.name, order.lastName, order.secondLastName)}</p>
                        <p><strong>Email:</strong> ${order.email}</p>
                        <p><strong>Phone:</strong> ${order.phone}</p>
                        <p><strong>Address:</strong> ${order.address}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Order Information</h6>
                        <p><strong>Created At:</strong> ${order.createdAt}</p>
                        <p><strong>Price without IVA:</strong> ${formatPrice(order.priceWithoutIVA)}</p>
                        <p><strong>IVA (21%):</strong> ${formatPrice(order.priceWithIVA - order.priceWithoutIVA)}</p>
                        <p class="text-success"><strong>Total Price with IVA:</strong> ${formatPrice(order.priceWithIVA)}</p>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="text-primary">Products</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${productsList}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function checkOrderExists(commandID) {
    // Using reusable function
    const request = buildRequest("checkOrder.php") + "?commandID=" + encodeURIComponent(commandID);
    
    return fetch(request, {
        method: 'GET'
    })
    .then(response => response.json())
    .then(result => result.exists);
}