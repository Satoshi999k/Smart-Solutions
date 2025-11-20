// Universal Search Functionality for Product Pages
// Redirects to search.php with search query for comprehensive results

document.addEventListener('DOMContentLoaded', function() {
    let searchInput = document.querySelector('.search-bar input');
    let searchIcon = document.querySelector('.search-icon');
    
    if (!searchInput || !searchIcon) return;
    
    // Function to perform search
    const performSearch = (searchTerm) => {
        if (searchTerm.trim() === '') {
            return;
        }
        // Redirect to search.php with query parameter
        window.location.href = 'search.php?q=' + encodeURIComponent(searchTerm.trim());
    };
    
    // Search on icon click
    searchIcon.addEventListener('click', () => {
        const searchTerm = searchInput.value;
        performSearch(searchTerm);
    });
    
    // Allow Enter key to trigger search
    searchInput.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchTerm = searchInput.value;
            performSearch(searchTerm);
        }
    });
});
