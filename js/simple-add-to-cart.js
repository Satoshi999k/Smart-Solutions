console.log('Simple add-to-cart.js loaded');

document.addEventListener('click', function(e) {
    let target = e.target.closest('a.ajax-add');
    if (!target) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    const productId = target.getAttribute('data-id');
    const productName = target.getAttribute('data-name');
    const productPrice = target.getAttribute('data-price');
    const productImage = target.getAttribute('data-image');
    
    console.log('Add to cart clicked:', { productId, productName, productPrice, productImage });
    
    if (!productId || !productName) {
        console.log('Missing product data');
        alert('Error: Missing product data');
        return;
    }
    
    // Ask for quantity
    const quantity = prompt('How many items do you want to add?', '1');
    if (quantity === null || quantity === '' || isNaN(quantity) || quantity < 1) {
        console.log('Quantity cancelled or invalid');
        return;
    }
    
    // Send to add_to_cart.php
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('quantity', parseInt(quantity));
    
    console.log('Sending to /ITP122/add_to_cart.php');
    
    fetch('/ITP122/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        console.log('Response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Response:', data);
        if (data.success) {
            alert('✓ Item added to cart!');
            // Update cart counter
            if (data.total_items) {
                const counter = document.querySelector('.cart-counter');
                if (counter) {
                    counter.textContent = data.total_items;
                }
            }
        } else {
            alert('Error: ' + (data.message || 'Could not add to cart'));
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        alert('Error adding to cart');
    });
});
