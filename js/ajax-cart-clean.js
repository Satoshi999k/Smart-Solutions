console.log('ajax-cart-clean.js loaded');

// PURE ADD TO CART - NO DOM MANIPULATION, NO ANIMATIONS, NO SIDE EFFECTS
document.addEventListener('click', function(e) {
    let target = e.target.closest('a.ajax-add');
    if (!target) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const productId = target.getAttribute('data-id');
    const productName = target.getAttribute('data-name');
    const productPrice = target.getAttribute('data-price');
    const productImage = target.getAttribute('data-image');
    
    console.log('Add to cart clicked:', {productId, productName, productPrice, productImage});
    
    if (!productId || !productName) {
        console.log('Missing product data');
        return;
    }
    
    // Fetch stock first
    fetch('/ITP122/get_product_stock.php?id=' + encodeURIComponent(productId))
    .then(res => res.json())
    .then(data => {
        const stock = (data && typeof data.stock !== 'undefined') ? parseInt(data.stock) : 10;
        console.log('Stock:', stock);
        showQuantityModal(productName, stock, function(quantity) {
            if (quantity > 0) {
                submitToCart(productId, productName, productPrice, productImage, quantity);
            }
        });
    })
    .catch(err => {
        console.log('Stock fetch error:', err);
        // Use default stock of 10 if fetch fails
        showQuantityModal(productName, 10, function(quantity) {
            if (quantity > 0) {
                submitToCart(productId, productName, productPrice, productImage, quantity);
            }
        });
    });
});

function showQuantityModal(productName, stock, callback) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    const box = document.createElement('div');
    box.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 8px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    `;
    
    const title = document.createElement('h3');
    title.textContent = productName;
    title.style.cssText = 'margin-top: 0; margin-bottom: 20px; font-size: 18px;';
    box.appendChild(title);
    
    const stockInfo = document.createElement('p');
    stockInfo.textContent = stock > 0 ? `Stock Available: ${stock} units` : 'Out of Stock';
    stockInfo.style.cssText = `
        margin: 0 0 20px 0;
        font-size: 14px;
        color: ${stock > 0 ? '#4caf50' : '#f44336'};
        font-weight: bold;
    `;
    box.appendChild(stockInfo);
    
    const label = document.createElement('label');
    label.textContent = 'Quantity:';
    label.style.cssText = 'display: block; margin-bottom: 10px; font-weight: bold;';
    box.appendChild(label);
    
    const input = document.createElement('input');
    input.type = 'number';
    input.min = '1';
    input.max = stock > 0 ? stock : '1';
    input.value = '1';
    input.disabled = stock <= 0;
    input.style.cssText = `
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 20px;
        ${stock <= 0 ? 'background: #f0f0f0; cursor: not-allowed;' : ''}
    `;
    box.appendChild(input);
    
    const btnContainer = document.createElement('div');
    btnContainer.style.cssText = 'display: flex; gap: 10px;';
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'flex: 1; padding: 10px; background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;';
    cancelBtn.onclick = () => modal.remove();
    btnContainer.appendChild(cancelBtn);
    
    const addBtn = document.createElement('button');
    addBtn.textContent = 'Add to Cart';
    addBtn.style.cssText = `
        flex: 1;
        padding: 10px;
        background: ${stock > 0 ? '#0062F6' : '#ccc'};
        color: white;
        border: none;
        border-radius: 4px;
        cursor: ${stock > 0 ? 'pointer' : 'not-allowed'};
        font-weight: bold;
    `;
    addBtn.disabled = stock <= 0;
    addBtn.onclick = () => {
        const qty = parseInt(input.value) || 1;
        if (qty > stock && stock > 0) {
            alert('Cannot add more than ' + stock + ' units');
            return;
        }
        modal.remove();
        callback(qty);
    };
    btnContainer.appendChild(addBtn);
    
    box.appendChild(btnContainer);
    modal.appendChild(box);
    document.body.appendChild(modal);
    
    input.focus();
}

function submitToCart(productId, productName, productPrice, productImage, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('quantity', quantity);
    
    console.log('Sending to /ITP122/add_to_cart.php with quantity:', quantity);
    
    fetch('/ITP122/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        console.log('Response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data && data.total_items) {
            const cartSpan = document.querySelector('.cart-counter');
            if (cartSpan) {
                cartSpan.textContent = data.total_items;
            }
        }
    })
    .catch(err => {
        console.log('Error:', err);
    });
}
