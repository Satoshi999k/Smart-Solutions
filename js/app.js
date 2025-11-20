let listProductHTML = document.querySelector('.listProduct');
let listCartHTML = document.querySelector('.listCart');
let iconCart = document.querySelector('.icon-cart');
let iconCartSpan = document.querySelector('.icon-cart span');
let body = document.querySelector('body');
let closeCart = document.querySelector('.close');
let products = [];
let cart = [];

let searchInput = document.querySelector('.search-bar input');
let searchIcon = document.querySelector('.search-icon');

// ============ JQUERY ANIMATIONS ============
// Ensure jQuery is loaded
if (typeof $ === 'undefined' && typeof jQuery !== 'undefined') {
    var $ = jQuery;
}

// Cart Toggle with Animation
if (iconCart) {
    iconCart.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof $ !== 'undefined') {
            $('body').fadeToggle(300, function() {
                $(this).toggleClass('showCart');
            });
        } else {
            body.classList.toggle('showCart');
        }
    });
}

if (closeCart) {
    closeCart.addEventListener('click', function(e) {
        e.preventDefault();
        if (typeof $ !== 'undefined') {
            $('body').fadeToggle(300, function() {
                $(this).toggleClass('showCart');
            });
        } else {
            body.classList.toggle('showCart');
        }
    });
}

    const addDataToHTML = (filteredProducts = products) => {
        listProductHTML.innerHTML = ''; 

        if (filteredProducts.length > 0) {
            filteredProducts.forEach((product, index) => {
                let newProduct = document.createElement('div');
                newProduct.dataset.id = product.id;
                newProduct.classList.add('item');
                newProduct.innerHTML =
                    `<img src="${product.image}" alt="">
                    <h2>${product.name}</h2>
                    <div class="price">$${product.price}</div>
                    <button class="addCart">Add To Cart</button>`;
                listProductHTML.appendChild(newProduct);
                
                // jQuery Animation for product cards
                if (typeof $ !== 'undefined') {
                    $(newProduct).hide().delay(index * 100).slideDown(400, function() {
                        $(this).addClass('animated fadeInUp');
                    });
                }
            });
        }
    }

    listProductHTML.addEventListener('click', (event) => {
        let positionClick = event.target;
        if (positionClick.classList.contains('addCart')) {
            let id_product = positionClick.parentElement.dataset.id;
            addToCart(id_product);
        }
    })

    searchInput.addEventListener('keyup', (event) => {
        let searchTerm = event.target.value.toLowerCase();
        let filteredProducts = products.filter(product =>
            product.name.toLowerCase().includes(searchTerm)
        );
        
        // jQuery Animation for search results
        if (typeof $ !== 'undefined' && listProductHTML) {
            $(listProductHTML).fadeOut(200, function() {
                addDataToHTML(filteredProducts);
                $(this).fadeIn(200);
            });
        } else {
            addDataToHTML(filteredProducts);
        }
    });

    searchIcon.addEventListener('click', () => {
        let searchTerm = searchInput.value.toLowerCase();
        let filteredProducts = products.filter(product =>
            product.name.toLowerCase().includes(searchTerm)
        );
        
        // jQuery Animation for search results
        if (typeof $ !== 'undefined' && listProductHTML) {
            $(listProductHTML).fadeOut(200, function() {
                addDataToHTML(filteredProducts);
                $(this).fadeIn(200);
            });
        } else {
            addDataToHTML(filteredProducts);
        }
    });
const addToCart = (product_id) => {
    let positionThisProductInCart = cart.findIndex((value) => value.product_id == product_id);
    if(cart.length <= 0){
        cart = [{
            product_id: product_id,
            quantity: 1
        }];
    }else if(positionThisProductInCart < 0){
        cart.push({
            product_id: product_id,
            quantity: 1
        });
    }else{
        cart[positionThisProductInCart].quantity = cart[positionThisProductInCart].quantity + 1;
    }
    
    // jQuery Animation for cart counter pulse
    if (typeof $ !== 'undefined') {
        var $counter = $('.icon-cart span, .cart-counter');
        $counter.stop(true, false).animate({
            fontSize: '24px'
        }, 200, function() {
            $(this).animate({
                fontSize: '20px'
            }, 200);
        });
    }
    
    addCartToHTML();
    addCartToMemory();
}
const addCartToMemory = () => {
    localStorage.setItem('cart', JSON.stringify(cart));
}
const addCartToHTML = () => {
    listCartHTML.innerHTML = '';
    let totalQuantity = 0;
    if(cart.length > 0){
        cart.forEach((item, index) => {
            totalQuantity = totalQuantity +  item.quantity;
            let newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.dataset.id = item.product_id;

            let positionProduct = products.findIndex((value) => value.id == item.product_id);
            let info = products[positionProduct];
            listCartHTML.appendChild(newItem);
            newItem.innerHTML = `
            <div class="image">
                    <img src="${info.image}">
                </div>
                <div class="name">
                ${info.name}
                </div>
                <div class="totalPrice">$${info.price * item.quantity}</div>
                <div class="quantity">
                    <span class="minus"><</span>
                    <span>${item.quantity}</span>
                    <span class="plus">></span>
                </div>
            `;
            
            // jQuery Animation for new cart items
            if (typeof $ !== 'undefined') {
                $(newItem).hide().slideDown(400);
            }
        })
    }
    iconCartSpan.innerText = totalQuantity;
}

listCartHTML.addEventListener('click', (event) => {
    let positionClick = event.target;
    if(positionClick.classList.contains('minus') || positionClick.classList.contains('plus')){
        let product_id = positionClick.parentElement.parentElement.dataset.id;
        let type = 'minus';
        if(positionClick.classList.contains('plus')){
            type = 'plus';
        }
        changeQuantityCart(product_id, type);
    }
})
const changeQuantityCart = (product_id, type) => {
    let positionItemInCart = cart.findIndex((value) => value.product_id == product_id);
    if(positionItemInCart >= 0){
        let info = cart[positionItemInCart];
        switch (type) {
            case 'plus':
                cart[positionItemInCart].quantity = cart[positionItemInCart].quantity + 1;
                break;
        
            default:
                let changeQuantity = cart[positionItemInCart].quantity - 1;
                if (changeQuantity > 0) {
                    cart[positionItemInCart].quantity = changeQuantity;
                }else{
                    cart.splice(positionItemInCart, 1);
                }
                break;
        }
    }
    addCartToHTML();
    addCartToMemory();
}

const initApp = () => {
    // get data product
    fetch('products.json')
    .then(response => response.json())
    .then(data => {
        products = data;
        addDataToHTML();

        // get data cart from memory
        if(localStorage.getItem('cart')){
            cart = JSON.parse(localStorage.getItem('cart'));
            addCartToHTML();
        }
    })
}
initApp();
