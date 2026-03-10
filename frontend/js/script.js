// SUPERGO SCRIPT GENERAL

document.addEventListener("DOMContentLoaded", () => {

    initFormValidation();
    initCart();
    updateCartCount();

});

// VALIDACION FORMULARIOS

function initFormValidation(){

    const forms = document.querySelectorAll(".needs-validation");

    forms.forEach(form => {

        form.addEventListener("submit", (event) => {

            if(!form.checkValidity()){
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add("was-validated");

        });

    });

}

// CARRITO

function initCart(){

    const buttons = document.querySelectorAll(".add-to-cart");

    buttons.forEach(button => {

        button.addEventListener("click", () => {

            const name = button.dataset.name;
            const price = parseFloat(button.dataset.price);

            addToCart(name, price);

        });

    });

}

// AGREGAR PRODUCTO

function addToCart(name, price){

    let cart = JSON.parse(localStorage.getItem("supergo_cart")) || [];

    const product = cart.find(p => p.name === name);

    if(product){

        product.qty += 1;

    }else{

        cart.push({
            name: name,
            price: price,
            qty: 1
        });

    }

    localStorage.setItem("supergo_cart", JSON.stringify(cart));

    updateCartCount();

}

// CONTADOR CARRITO

function updateCartCount(){

    const cart = JSON.parse(localStorage.getItem("supergo_cart")) || [];

    let total = 0;

    cart.forEach(p => {
        total += p.qty;
    });

    const badge = document.querySelector("#cart-count");

    if(badge){
        badge.textContent = total;
    }

}

// MOSTRAR CARRITO

function renderCart(){

    const cart = JSON.parse(localStorage.getItem("supergo_cart")) || [];

    const table = document.querySelector("#cart-table");

    if(!table) return;

    table.innerHTML = "";

    let total = 0;

    cart.forEach(item => {

        const subtotal = item.price * item.qty;

        total += subtotal;

        const row = `
        <tr>
            <td>${item.name}</td>
            <td>$${item.price}</td>
            <td>${item.qty}</td>
            <td>$${subtotal}</td>
        </tr>
        `;

        table.innerHTML += row;

    });

    const totalElement = document.querySelector("#cart-total");

    if(totalElement){
        totalElement.textContent = "$" + total;
    }

}

// LIMPIAR CARRITO

function clearCart(){

    localStorage.removeItem("supergo_cart");

    renderCart();
    updateCartCount();

}