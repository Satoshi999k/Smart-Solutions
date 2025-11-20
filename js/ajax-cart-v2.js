console.log('ajax-cart-v2.js loaded');

// Main click handler for add to cart buttons
document.addEventListener('click', function(e) {
    try {
        // Find the ajax-add link - handle both direct clicks and clicks through button
        let target = null;
        
        if (e.target.classList && e.target.classList.contains('ajax-add')) {
            target = e.target;
        } else if (e.target.closest && e.target.closest('a.ajax-add')) {
            target = e.target.closest('a.ajax-add');
        }
        
        if (!target) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        console.log('=== ADD TO CART CLICKED ===');
        
        // Get product data from attributes
        const productId = target.getAttribute('data-id');
        const productName = target.getAttribute('data-name');
        const productPrice = target.getAttribute('data-price');
        const productImage = target.getAttribute('data-image');
        const productStock = target.getAttribute('data-stock');
        
        console.log('Product ID:', productId);
        console.log('Product Name:', productName);
        console.log('Product Price:', productPrice);
        console.log('Product Image:', productImage);
        console.log('Product Stock:', productStock);
        
        if (!productId || !productName) {
            console.error('Missing product ID or name');
            showToast('Missing product information', 'error');
            return;
        }
        
        // If stock is provided as data attribute, use it. Otherwise fetch from server
        if (productStock) {
            console.log('Using stock from data attribute:', productStock);
            showQuantityModal(productName, parseInt(productStock), function(quantity, cancelled) {
                if (!cancelled) {
                    submitAddToCart(productId, productName, productPrice, productImage, quantity);
                }
            });
        } else {
            console.log('Fetching stock from server...');
            fetchStock(productId, productName, productPrice, productImage);
        }
        
    } catch (err) {
        console.error('Click handler error:', err);
        showToast('An error occurred', 'error');
    }
});

function fetchStock(productId, productName, productPrice, productImage) {
    const url = '/ITP122/get_product_stock.php?id=' + encodeURIComponent(productId);
    console.log('Fetching stock from:', url);
    
    fetch(url)
        .then(res => {
            console.log('Stock response status:', res.status);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            console.log('Stock data received:', data);
            const stock = (data && typeof data.stock !== 'undefined') ? parseInt(data.stock) : 10;
            console.log('Available stock:', stock);
            
            showQuantityModal(productName, stock, function(quantity, cancelled) {
                if (!cancelled) {
                    submitAddToCart(productId, productName, productPrice, productImage, quantity);
                }
            });
        })
        .catch(err => {
            console.error('Stock fetch error:', err);
            console.log('Using default stock of 10');
            showQuantityModal(productName, 10, function(quantity, cancelled) {
                if (!cancelled) {
                    submitAddToCart(productId, productName, productPrice, productImage, quantity);
                }
            });
        });
}

function submitAddToCart(productId, productName, productPrice, productImage, quantity) {
    console.log('=== SUBMITTING TO CART ===');
    console.log('Product ID:', productId);
    console.log('Quantity:', quantity);
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('quantity', quantity);
    
    console.log('Form data entries:', Array.from(formData.entries()));
    
    const url = '/ITP122/add_to_cart.php';
    console.log('Posting to:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(res => {
            console.log('Add to cart response status:', res.status);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            console.log('Add to cart response data:', data);
            
            if (data && data.success) {
                showToast(data.message || 'Added to cart!', 'success');
                
                // Update cart counter if it exists
                const cartSpan = document.querySelector('.cart-counter');
                if (cartSpan && typeof data.total_items !== 'undefined') {
                    cartSpan.textContent = data.total_items;
                    console.log('Updated cart counter to:', data.total_items);
                }
            } else {
                showToast(data.message || 'Could not add to cart', 'error');
            }
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            showToast('Error: ' + err.message, 'error');
        });
}

function showQuantityModal(productName, stock, callback) {
    console.log('=== SHOWING QUANTITY MODAL ===');
    console.log('Product:', productName);
    console.log('Stock:', stock);
    
    // Prevent duplicate modals
    if (document.getElementById('qty-modal-overlay')) {
        console.log('Modal already exists, skipping');
        return;
    }
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'qty-modal-overlay';
    overlay.style.cssText = `
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    // Create modal box
    const box = document.createElement('div');
    box.style.cssText = `
        background: white;
        border-radius: 8px;
        padding: 24px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        font-family: Arial, sans-serif;
    `;
    
    // Product name
    const title = document.createElement('h3');
    title.textContent = productName;
    title.style.cssText = 'margin: 0 0 12px 0; font-size: 18px;';
    box.appendChild(title);
    
    // Stock info
    const stockInfo = document.createElement('p');
    stockInfo.style.cssText = 'margin: 8px 0; font-size: 14px; color: #666;';
    if (stock > 0) {
        stockInfo.textContent = 'Available: ' + stock + ' units';
    } else {
        stockInfo.textContent = 'Out of Stock';
        stockInfo.style.color = '#d32f2f';
    }
    box.appendChild(stockInfo);
    
    // Quantity label
    const label = document.createElement('label');
    label.textContent = 'Quantity:';
    label.style.cssText = 'display: block; margin: 16px 0 8px 0; font-weight: bold;';
    box.appendChild(label);
    
    // Quantity input
    const input = document.createElement('input');
    input.type = 'number';
    input.min = '1';
    input.max = stock > 0 ? stock : '1';
    input.value = '1';
    input.style.cssText = 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; box-sizing: border-box;';
    box.appendChild(input);
    
    // Buttons container
    const btnContainer = document.createElement('div');
    btnContainer.style.cssText = 'display: flex; gap: 8px; margin-top: 20px;';
    
    // Cancel button
    const cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = `
        flex: 1;
        padding: 10px;
        background: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    `;
    cancelBtn.addEventListener('click', function() {
        console.log('Cancel clicked');
        overlay.remove();
        callback(0, true);
    });
    btnContainer.appendChild(cancelBtn);
    
    // Add to Cart button
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
        font-size: 14px;
        font-weight: bold;
    `;
    addBtn.disabled = stock <= 0;
    
    addBtn.addEventListener('click', function() {
        let qty = parseInt(input.value, 10) || 1;
        
        if (qty < 1) qty = 1;
        if (stock > 0 && qty > stock) {
            alert('Cannot add more than ' + stock + ' units');
            return;
        }
        
        console.log('Add button clicked, quantity:', qty);
        overlay.remove();
        callback(qty, false);
    });
    btnContainer.appendChild(addBtn);
    
    box.appendChild(btnContainer);
    overlay.appendChild(box);
    document.body.appendChild(overlay);
    
    // Focus on input
    input.focus();
    
    // Allow Enter to submit, Esc to cancel
    input.addEventListener('keydown', function(ev) {
        if (ev.key === 'Enter' && stock > 0) {
            ev.preventDefault();
            addBtn.click();
        }
        if (ev.key === 'Escape') {
            ev.preventDefault();
            cancelBtn.click();
        }
    });
}

function showToast(message, type) {
    console.log('Showing toast:', type, message);
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        z-index: 10000;
        animation: slideUp 0.3s ease;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Auto-remove after 3 seconds
    setTimeout(function() {
        toast.style.animation = 'slideDown 0.3s ease';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}

// Add animation styles
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
        @keyframes slideDown {
            from {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
        }
    `;
    document.head.appendChild(style);
}
