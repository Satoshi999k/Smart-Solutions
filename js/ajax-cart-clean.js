console.log('ajax-cart-clean.js loaded');

// Restore guest cart from localStorage on page load
// DISABLED: Cart counter is now controlled by PHP session, not localStorage
/*
document.addEventListener('DOMContentLoaded', function() {
    const guestCartBackup = localStorage.getItem('guest_cart_backup');
    if (guestCartBackup) {
        try {
            const cart = JSON.parse(guestCartBackup);
            // Update cart counter if cart exists
            const cartSpan = document.querySelector('.cart-counter');
            if (cartSpan && cart && cart.length > 0) {
                cartSpan.textContent = cart.length;
                console.log('Restored guest cart from localStorage:', cart);
            }
        } catch (e) {
            console.log('Error parsing guest cart from localStorage:', e);
        }
    }
});
*/

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
    // Load Material Icons font if not already loaded
    if (!document.querySelector('link[href*="material-icons"]')) {
        const link = document.createElement('link');
        link.href = 'https://fonts.googleapis.com/icon?family=Material+Icons';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    }
    
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease-out;
    `;
    
    const box = document.createElement('div');
    box.style.cssText = `
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    `;
    
    // Header with gradient matching your site
    const header = document.createElement('div');
    header.style.cssText = `
        background: linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%);
        padding: 35px;
        position: relative;
        overflow: hidden;
    `;
    
    // Decorative circles in header
    const decoration1 = document.createElement('div');
    decoration1.style.cssText = `
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50px;
        right: -50px;
    `;
    header.appendChild(decoration1);
    
    const decoration2 = document.createElement('div');
    decoration2.style.cssText = `
        position: absolute;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        bottom: -30px;
        left: -30px;
    `;
    header.appendChild(decoration2);
    
    const title = document.createElement('h3');
    title.textContent = productName;
    title.style.cssText = `
        margin: 0;
        color: white;
        font-size: 24px;
        font-weight: 700;
        position: relative;
        z-index: 1;
        letter-spacing: -0.5px;
        line-height: 1.3;
    `;
    header.appendChild(title);
    box.appendChild(header);
    
    // Content section
    const content = document.createElement('div');
    content.style.cssText = `
        padding: 35px;
    `;
    
    const stockInfo = document.createElement('div');
    stockInfo.style.cssText = `
        background: ${stock > 0 ? 'linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%)' : 'linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%)'};
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 28px;
        border-left: 5px solid ${stock > 0 ? '#4caf50' : '#f44336'};
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    `;
    
    const stockIcon = document.createElement('i');
    stockIcon.className = 'material-icons';
    stockIcon.style.cssText = `
        font-size: 28px;
        color: ${stock > 0 ? '#4caf50' : '#f44336'};
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    stockIcon.textContent = stock > 0 ? 'check_circle' : 'cancel';
    stockInfo.appendChild(stockIcon);
    
    const stockText = document.createElement('div');
    stockText.style.cssText = `
        color: ${stock > 0 ? '#2e7d32' : '#c62828'};
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 0.3px;
    `;
    stockText.textContent = stock > 0 ? `Stock Available: ${stock} units` : 'Out of Stock';
    stockInfo.appendChild(stockText);
    content.appendChild(stockInfo);
    
    const label = document.createElement('label');
    label.textContent = 'Select Quantity';
    label.style.cssText = `
        display: block;
        margin-bottom: 14px;
        font-weight: 700;
        color: #2c3e50;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    `;
    content.appendChild(label);
    
    // Input with enhanced styling
    const inputWrapper = document.createElement('div');
    inputWrapper.style.cssText = `
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 32px;
    `;
    
    const input = document.createElement('input');
    input.type = 'number';
    input.min = '1';
    input.max = stock > 0 ? stock : '1';
    input.value = '1';
    input.disabled = stock <= 0;
    input.style.cssText = `
        flex: 1;
        padding: 16px;
        border: 2px solid #e8e8e8;
        border-radius: 10px;
        box-sizing: border-box;
        font-size: 18px;
        font-weight: 600;
        transition: all 0.3s ease;
        ${stock <= 0 ? 'background: #f8f8f8; cursor: not-allowed; color: #bdbdbd;' : 'background: white; color: #2c3e50;'}
    `;
    input.addEventListener('focus', function() {
        if (stock > 0) {
            this.style.borderColor = '#007BFF';
            this.style.boxShadow = '0 0 0 3px rgba(0, 123, 255, 0.1)';
        }
    });
    input.addEventListener('blur', function() {
        this.style.borderColor = '#e8e8e8';
        this.style.boxShadow = 'none';
    });
    inputWrapper.appendChild(input);
    
    // Quick amount buttons
    const quickBtns = document.createElement('div');
    quickBtns.style.cssText = `
        display: flex;
        gap: 8px;
    `;
    
    [1, 5, 10].forEach(qty => {
        if (qty <= stock || stock === 0) {
            const btn = document.createElement('button');
            btn.textContent = qty;
            btn.style.cssText = `
                padding: 12px 14px;
                background: white;
                border: 2px solid #e8e8e8;
                border-radius: 8px;
                cursor: ${stock > 0 ? 'pointer' : 'not-allowed'};
                font-weight: 700;
                transition: all 0.25s ease;
                color: #007BFF;
                font-size: 14px;
                min-width: 45px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            `;
            btn.disabled = stock <= 0;
            btn.addEventListener('click', () => {
                input.value = qty;
                input.focus();
            });
            btn.addEventListener('mouseover', function() {
                if (!this.disabled) {
                    this.style.background = '#007BFF';
                    this.style.color = 'white';
                    this.style.borderColor = '#007BFF';
                    this.style.boxShadow = '0 8px 16px rgba(0, 123, 255, 0.25)';
                    this.style.transform = 'translateY(-2px)';
                }
            });
            btn.addEventListener('mouseout', function() {
                this.style.background = 'white';
                this.style.color = '#007BFF';
                this.style.borderColor = '#e8e8e8';
                this.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.05)';
                this.style.transform = 'translateY(0)';
            });
            quickBtns.appendChild(btn);
        }
    });
    
    inputWrapper.appendChild(quickBtns);
    content.appendChild(inputWrapper);
    box.appendChild(content);
    
    // Button group
    const btnContainer = document.createElement('div');
    btnContainer.style.cssText = `
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 14px;
        padding: 0 35px 35px;
    `;
    
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = `
        padding: 16px;
        background: linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%);
        color: #2c3e50;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    `;
    cancelBtn.addEventListener('mouseover', function() {
        this.style.background = 'linear-gradient(135deg, #eeeeee 0%, #e0e0e0 100%)';
        this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
        this.style.transform = 'translateY(-2px)';
    });
    cancelBtn.addEventListener('mouseout', function() {
        this.style.background = 'linear-gradient(135deg, #f5f5f5 0%, #eeeeee 100%)';
        this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        this.style.transform = 'translateY(0)';
    });
    cancelBtn.onclick = () => modal.remove();
    btnContainer.appendChild(cancelBtn);
    
    const addBtn = document.createElement('button');
    addBtn.textContent = 'Add to Cart';
    addBtn.style.cssText = `
        padding: 16px;
        background: ${stock > 0 ? 'linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%)' : 'linear-gradient(135deg, #bdbdbd 0%, #9e9e9e 100%)'};
        color: white;
        border: none;
        border-radius: 10px;
        cursor: ${stock > 0 ? 'pointer' : 'not-allowed'};
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    `;
    addBtn.disabled = stock <= 0;
    
    if (stock > 0) {
        addBtn.addEventListener('mouseover', function() {
            this.style.background = 'linear-gradient(45deg, #0056b3 0%, #003f87 25%, #002d63 50%, #003f87 75%, #0056b3 100%)';
            this.style.boxShadow = '0 8px 20px rgba(0, 123, 255, 0.4)';
            this.style.transform = 'translateY(-2px)';
        });
        addBtn.addEventListener('mouseout', function() {
            this.style.background = 'linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%)';
            this.style.boxShadow = '0 4px 12px rgba(0, 123, 255, 0.3)';
            this.style.transform = 'translateY(0)';
        });
    }
    
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
    
    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
    
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
        
        // Save cart item to localStorage for persistence (guest cart backup)
        if (data && data.cart) {
            localStorage.setItem('guest_cart_backup', JSON.stringify(data.cart));
            console.log('Saved guest cart to localStorage');
        }
    })
    .catch(err => {
        console.log('Error:', err);
    });
}
