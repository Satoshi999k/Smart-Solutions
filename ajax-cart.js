/**
 * ajax-cart.js - AJAX Add-to-Cart functionality for all product pages
 * Intercepts clicks on links with class "ajax-add" and posts to add_to_cart.php
 * Shows a non-blocking toast notification and updates cart counter.
 */

document.addEventListener('click', function(e) {
    var target = e.target.closest && e.target.closest('a.ajax-add');
    if (!target) return;
    e.preventDefault();

    var id = target.getAttribute('data-id');
    var name = target.getAttribute('data-name');
    var price = target.getAttribute('data-price');
    var image = target.getAttribute('data-image');

    // Show quantity modal before adding
    showQuantityModal(name, function(quantity, cancelled) {
        if (cancelled) return; // user cancelled

        var form = new FormData();
        form.append('product_id', id);
        form.append('product_name', name);
        form.append('product_price', price);
        form.append('product_image', image);
        form.append('quantity', quantity);

        fetch('add_to_cart.php', {
            method: 'POST',
            body: form
        }).then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.success) {
                showCartToast(data.message || 'Added to cart');
                // Update cart counter if present (unique products count returned)
                var cartSpan = document.querySelector('.cart-counter');
                if (cartSpan && typeof data.total_items !== 'undefined') {
                    cartSpan.textContent = data.total_items;
                }
            } else {
                showCartToast((data && data.message) || 'Could not add to cart');
            }
        }).catch(function(err) {
            console.error(err);
            showCartToast('Error adding to cart');
        });
    });
});

/**
 * Show a small modal to choose quantity with Add and Cancel buttons.
 * callback(quantity:int, cancelled:bool)
 */
function showQuantityModal(productName, callback) {
    // prevent multiple modals
    if (document.getElementById('ajax-qty-modal')) return;

    var overlay = document.createElement('div');
    overlay.id = 'ajax-qty-modal';
    overlay.style.position = 'fixed';
    overlay.style.left = '0';
    overlay.style.top = '0';
    overlay.style.right = '0';
    overlay.style.bottom = '0';
    overlay.style.background = 'rgba(0,0,0,0.4)';
    overlay.style.zIndex = '10000';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';

    var box = document.createElement('div');
    box.style.background = '#fff';
    box.style.borderRadius = '8px';
    box.style.padding = '18px';
    box.style.width = '320px';
    box.style.boxShadow = '0 8px 30px rgba(0,0,0,0.25)';
    box.style.textAlign = 'center';
    box.style.fontFamily = 'Arial, sans-serif';
    box.style.position = 'relative';

    var title = document.createElement('div');
    title.textContent = 'Quantity for: ' + productName;
    title.style.marginBottom = '12px';
    title.style.fontWeight = '600';

    // Add an X (close) button in the top-right corner
    var closeBtn = document.createElement('button');
    closeBtn.innerHTML = '\u00d7'; // multiplication 'x'
    closeBtn.title = 'Close';
    closeBtn.style.position = 'absolute';
    closeBtn.style.top = '8px';
    closeBtn.style.right = '8px';
    closeBtn.style.background = 'transparent';
    closeBtn.style.border = 'none';
    closeBtn.style.fontSize = '20px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.lineHeight = '1';
    closeBtn.style.color = '#666';
    closeBtn.style.padding = '2px 6px';
    closeBtn.style.borderRadius = '4px';
    closeBtn.addEventListener('click', function() {
        if (document.body.contains(overlay)) document.body.removeChild(overlay);
        callback(0, true);
    });
    box.appendChild(closeBtn);

    var input = document.createElement('input');
    input.type = 'number';
    input.min = '1';
    input.value = '1';
    input.style.width = '80px';
    input.style.fontSize = '16px';
    input.style.padding = '8px';
    input.style.marginBottom = '12px';

    var btnRow = document.createElement('div');
    btnRow.style.display = 'flex';
    btnRow.style.justifyContent = 'space-between';

    var cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.background = '#f0f0f0';
    cancelBtn.style.border = 'none';
    cancelBtn.style.padding = '10px 14px';
    cancelBtn.style.borderRadius = '6px';
    cancelBtn.style.cursor = 'pointer';

    var addBtn = document.createElement('button');
    addBtn.textContent = 'Add to Cart';
    addBtn.style.background = '#0062F6';
    addBtn.style.color = '#fff';
    addBtn.style.border = 'none';
    addBtn.style.padding = '10px 14px';
    addBtn.style.borderRadius = '6px';
    addBtn.style.cursor = 'pointer';

    btnRow.appendChild(cancelBtn);
    btnRow.appendChild(addBtn);

    box.appendChild(title);
    box.appendChild(input);
    box.appendChild(btnRow);
    overlay.appendChild(box);
    document.body.appendChild(overlay);

    // focus and select input
    setTimeout(function() { input.focus(); input.select(); }, 50);

    cancelBtn.addEventListener('click', function() {
        document.body.removeChild(overlay);
        callback(0, true);
    });

    overlay.addEventListener('click', function(ev) {
        if (ev.target === overlay) {
            document.body.removeChild(overlay);
            callback(0, true);
        }
    });

    addBtn.addEventListener('click', function() {
        var q = parseInt(input.value, 10) || 1;
        if (q < 1) q = 1;
        document.body.removeChild(overlay);
        callback(q, false);
    });

    // allow Enter to submit and Esc to cancel
    input.addEventListener('keydown', function(ev) {
        if (ev.key === 'Enter') { ev.preventDefault(); addBtn.click(); }
        if (ev.key === 'Escape') { ev.preventDefault(); cancelBtn.click(); }
    });
}

/**
 * Display a toast notification at top-center with larger size
 */
function showCartToast(msg) {
    var t = document.getElementById('ajax-cart-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'ajax-cart-toast';
        t.style.position = 'fixed';
        t.style.top = '20px';
        t.style.left = '50%';
        t.style.transform = 'translateX(-50%)';
        t.style.background = '#545252';
        t.style.color = '#fff';
        t.style.padding = '18px 28px';
        t.style.borderRadius = '8px';
        t.style.zIndex = '9999';
        t.style.fontSize = '16px';
        t.style.fontWeight = 'bold';
        t.style.fontFamily = 'Arial, sans-serif';
        t.style.transition = 'all 0.3s ease';
        t.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
        t.style.maxWidth = '400px';
        t.style.wordWrap = 'break-word';
        t.style.textAlign = 'center';
        t.style.borderLeft = '4px solid #0062F6';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.opacity = '1';
    t.style.display = 'block';
    t.style.animation = 'slideDown 0.3s ease';
    
    // Add CSS animation if not already present
    if (!document.getElementById('toast-animation-style')) {
        var style = document.createElement('style');
        style.id = 'toast-animation-style';
        style.textContent = `
            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
            }
            @keyframes slideUp {
                from {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(-50%) translateY(-30px);
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Auto-hide after 3 seconds
    if (t._hideTimeout) clearTimeout(t._hideTimeout);
    t._hideTimeout = setTimeout(function() {
        t.style.animation = 'slideUp 0.3s ease';
        setTimeout(function() { t.style.display = 'none'; }, 300);
    }, 3000);
}
