function buildRequest(phpFile) {
    const ip = "iot.electronics.cat";
    const folder = "eCommerce/app/srv";
    return "https://" + ip + "/" + folder + "/" + phpFile;
}

function formatPrice(price) {
    return price.toFixed(2) + '€';
}
function buildFullName(name, lastName, secondLastName) {
    return name + ' ' + lastName + ' ' + secondLastName;
}