/**
 * Page Load Animation - Complete Script
 * Handles header, body, and footer animations on page load
 */

(function() {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageAnimation);
    } else {
        initPageAnimation();
    }

    function initPageAnimation() {
        // Animate Header
        animateHeader();
        
        // Animate Body Content
        animateBodyContent();
        
        // Animate Footer
        animateFooter();
    }

    function animateHeader() {
        const header = document.querySelector('header');
        
        if (!header) return;

        // Set initial state - header above viewport (keep position sticky)
        header.style.top = '-150px';
        header.style.opacity = '0';
        header.style.visibility = 'visible';
        
        // Trigger animation after a tiny delay
        setTimeout(function() {
            header.style.transition = 'all 0.7s cubic-bezier(0.34, 1.56, 0.64, 1)';
            header.style.top = '0';
            header.style.opacity = '1';
        }, 50);
    }

    function animateBodyContent() {
        const body = document.querySelector('body');
        const menu = document.querySelector('.menu');
        const carousel = document.querySelector('.carousel-container');
        const products = document.querySelector('.product-container');
        
        if (!body) return;

        // Fade in body
        body.style.opacity = '0';
        body.style.transition = 'opacity 0.6s ease-in';
        
        setTimeout(function() {
            body.style.opacity = '1';
        }, 100);

        // Animate menu bar sliding down
        if (menu) {
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(-30px)';
            menu.style.transition = 'all 0.6s ease-out 0.3s';
            
            setTimeout(function() {
                menu.style.opacity = '1';
                menu.style.transform = 'translateY(0)';
            }, 100);
        }

        // Animate carousel
        if (carousel) {
            carousel.style.opacity = '0';
            carousel.style.transform = 'scale(0.95)';
            carousel.style.transition = 'all 0.7s ease-out 0.5s';
            
            setTimeout(function() {
                carousel.style.opacity = '1';
                carousel.style.transform = 'scale(1)';
            }, 100);
        }

        // Animate product container
        if (products) {
            products.style.opacity = '0';
            products.style.transform = 'translateY(30px)';
            products.style.transition = 'all 0.7s ease-out 0.7s';
            
            setTimeout(function() {
                products.style.opacity = '1';
                products.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    function animateFooter() {
        const footer = document.querySelector('footer.footer');
        const copyright = document.querySelector('.copyright');
        
        if (!footer) return;

        // Set initial state
        footer.style.opacity = '0';
        footer.style.transform = 'translateY(50px)';
        
        // Trigger animation after other animations
        setTimeout(function() {
            footer.style.transition = 'all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)';
            footer.style.opacity = '1';
            footer.style.transform = 'translateY(0)';
        }, 1200);

        // Animate copyright
        if (copyright) {
            copyright.style.opacity = '0';
            
            setTimeout(function() {
                copyright.style.transition = 'opacity 0.6s ease-out';
                copyright.style.opacity = '1';
            }, 1400);
        }
    }
})();

