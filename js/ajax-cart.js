console.log('ajax-cart.js loaded - Pure add to cart only');

// Add to cart - NO alerts, NO modal, NO animations, NO interference
document.addEventListener('click', function(e) {
    let target = e.target.closest('a.ajax-add');
    if (!target) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const productId = target.getAttribute('data-id');
    const productName = target.getAttribute('data-name');
    const productPrice = target.getAttribute('data-price');
    const productImage = target.getAttribute('data-image');
    
    if (!productId || !productName) {
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('quantity', 1);
    
    fetch('/ITP122/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.success) {
            // Update cart counter silently
            const cartSpan = document.querySelector('.cart-counter');
            if (cartSpan && data.total_items) {
                cartSpan.textContent = data.total_items;
            }
            
            // Show simple notification without alert
            showNotification('Item added to cart!');
        } else {
            showNotification('Could not add to cart', 'error');
        }
    })
    .catch(err => showNotification('Error adding to cart', 'error'));
});

// Simple notification that doesn't interfere with page animations
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'error' ? '#f44336' : '#4caf50'};
        color: white;
        padding: 15px 20px;
        border-radius: 4px;
        font-size: 14px;
        z-index: 10000;
        pointer-events: none;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Auto remove after 2 seconds
    setTimeout(() => {
        notification.remove();
    }, 2000);
}
