// Real-time Search Functionality for search.php
// Allows users to search again without page reload

document.addEventListener('DOMContentLoaded', function() {
    let searchInput = document.querySelector('.search-bar input');
    let searchIcon = document.querySelector('.search-icon');
    
    if (!searchInput || !searchIcon) return;
    
    // Get the current products displayed on the page
    const getAllProducts = () => {
        const productCards = document.querySelectorAll('.product-grid .product-card');
        const products = [];
        
        productCards.forEach(card => {
            const h4 = card.querySelector('h4');
            const priceP = card.querySelector('p:nth-of-type(1)'); // Price paragraph
            const categoryP = card.querySelector('p:nth-of-type(2)'); // Category paragraph
            
            if (h4) {
                products.push({
                    element: card,
                    name: h4.textContent.toLowerCase(),
                    price: priceP ? priceP.textContent.toLowerCase() : '',
                    category: categoryP ? categoryP.textContent.toLowerCase() : ''
                });
            }
        });
        
        return products;
    };
    
    // Filter and display products in real-time
    const filterProductsRealTime = (searchTerm) => {
        const products = getAllProducts();
        const queryLower = searchTerm.toLowerCase().trim();
        let matchCount = 0;
        
        products.forEach(product => {
            const matches = 
                product.name.includes(queryLower) || 
                product.price.includes(queryLower) ||
                product.category.includes(queryLower);
            
            if (queryLower === '') {
                // Show all if search is empty
                product.element.style.display = '';
                matchCount++;
            } else if (matches) {
                product.element.style.display = '';
                matchCount++;
            } else {
                product.element.style.display = 'none';
            }
        });
        
        // Update result count
        updateResultCount(queryLower, matchCount);
        
        // Show/hide no results message
        updateNoResultsMessage(queryLower, matchCount);
    };
    
    // Update the result count display
    const updateResultCount = (searchTerm, matchCount) => {
        let resultCountDiv = document.getElementById('search-result-count');
        
        if (!resultCountDiv) {
            resultCountDiv = document.createElement('p');
            resultCountDiv.id = 'search-result-count';
            resultCountDiv.style.cssText = 'color: #666; margin-bottom: 30px; font-size: 14px;';
            
            const resultsContainer = document.querySelector('div[style*="padding: 30px"]');
            if (resultsContainer) {
                resultsContainer.insertBefore(resultCountDiv, resultsContainer.querySelector('.product-grid, div[style*="text-align: center"]'));
            }
        }
        
        if (searchTerm === '') {
            resultCountDiv.textContent = '';
        } else {
            resultCountDiv.innerHTML = `Found <strong>${matchCount}</strong> product(s) matching "<strong>${escapeHtml(searchTerm)}</strong>"`;
        }
    };
    
    // Show/hide no results message
    const updateNoResultsMessage = (searchTerm, matchCount) => {
        let noResultsDiv = document.getElementById('no-search-results');
        const productGrid = document.querySelector('.product-grid');
        
        if (matchCount === 0 && searchTerm.trim() !== '') {
            // Show no results message
            if (!noResultsDiv) {
                noResultsDiv = document.createElement('div');
                noResultsDiv.id = 'no-search-results';
                noResultsDiv.style.cssText = 'text-align: center; padding: 50px 20px; background-color: #f5f5f5; border-radius: 8px; grid-column: 1 / -1;';
                noResultsDiv.innerHTML = `
                    <h3 style="color: #999; margin-bottom: 10px;">No Products Found</h3>
                    <p style="color: #666;">Sorry, we couldn't find any products matching "<strong>${escapeHtml(searchTerm)}</strong>"</p>
                `;
                if (productGrid) {
                    productGrid.appendChild(noResultsDiv);
                }
            } else {
                noResultsDiv.innerHTML = `
                    <h3 style="color: #999; margin-bottom: 10px;">No Products Found</h3>
                    <p style="color: #666;">Sorry, we couldn't find any products matching "<strong>${escapeHtml(searchTerm)}</strong>"</p>
                `;
                noResultsDiv.style.display = '';
            }
        } else if (noResultsDiv) {
            noResultsDiv.style.display = 'none';
        }
    };
    
    // Escape HTML to prevent XSS
    const escapeHtml = (text) => {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    };
    
    // Search on keyup (real-time search)
    searchInput.addEventListener('keyup', (event) => {
        const searchTerm = event.target.value;
        filterProductsRealTime(searchTerm);
    });
    
    // Search on icon click
    searchIcon.addEventListener('click', () => {
        const searchTerm = searchInput.value;
        filterProductsRealTime(searchTerm);
    });
    
    // Allow Enter key to trigger search
    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchTerm = searchInput.value;
            filterProductsRealTime(searchTerm);
        }
    });
});
