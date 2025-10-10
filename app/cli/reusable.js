function buildRequest(phpFile) {
    const ip = "192.168.1.86";
    const folder = "eCommerce/app/srv";
    return "http://" + ip + "/" + folder + "/" + phpFile;
}

function formatPrice(price) {
    return price.toFixed(2) + '€';
}

function buildFullName(name, lastName, secondLastName) {
    return name + ' ' + lastName + ' ' + secondLastName;
}