/**
 * jQuery Animations Library
 * Comprehensive animation effects for SmartSolutions e-commerce
 * Requires jQuery to be loaded before this script
 */

$(document).ready(function() {
    // ============ PAGE LOAD ANIMATIONS ============
    $('body').fadeIn(500);
    
    // Animate hero section with staggered effect
    $('.hero, .banner, .slider').slideDown(700, function() {
        $(this).addClass('loaded');
    });
    
    // ============ PRODUCT CARD ANIMATIONS ============
    
    // Product cards stagger animation
    $('.product, .product-card, .item, .box').each(function(index) {
        $(this).hide().delay(index * 100).slideDown(400, function() {
            $(this).addClass('animated fadeInUp');
        });
    });
    
    // Product hover effects
    $(document).on('mouseenter', '.product, .product-card, .item, .box', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(-10px)',
            boxShadow: '0 10px 30px rgba(0, 0, 0, 0.15)'
        }, 300);
    });
    
    $(document).on('mouseleave', '.product, .product-card, .item, .box', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(0)',
            boxShadow: 'none'
        }, 300);
    });
    
    // Product image zoom on hover
    $(document).on('mouseenter', '.product img, .product-card img, .item img', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1.05)'
        }, 300);
    });
    
    $(document).on('mouseleave', '.product img, .product-card img, .item img', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1)'
        }, 300);
    });
    
    // ============ BUTTON ANIMATIONS ============
    
    // Button hover effects
    $(document).on('mouseenter', 'button, .btn, a.button', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(-2px)',
            boxShadow: '0 5px 15px rgba(0, 0, 0, 0.2)'
        }, 200);
    });
    
    $(document).on('mouseleave', 'button, .btn, a.button', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(0)',
            boxShadow: 'none'
        }, 200);
    });
    
    // Add to cart button animation
    $(document).on('click', '.addCart, .add-to-cart, .add-to-cart-btn', function() {
        var $btn = $(this);
        var originalText = $btn.text();
        
        $btn.stop(true, false).animate({
            width: '+=5'
        }, 150, function() {
            $btn.text('Added! ✓');
            $btn.addClass('added-to-cart');
            
            setTimeout(function() {
                $btn.animate({
                    width: '-=5'
                }, 150, function() {
                    $btn.text(originalText);
                    $btn.removeClass('added-to-cart');
                });
            }, 1500);
        });
    });
    
    // ============ FORM ANIMATIONS ============
    
    // Form input focus animations
    $(document).on('focus', 'input[type="text"], input[type="email"], input[type="password"], input[type="number"], textarea, select', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(-2px)',
            boxShadow: '0 5px 15px rgba(0, 98, 246, 0.2)'
        }, 300);
    });
    
    $(document).on('blur', 'input[type="text"], input[type="email"], input[type="password"], input[type="number"], textarea, select', function() {
        $(this).stop(true, false).animate({
            transform: 'translateY(0)',
            boxShadow: 'none'
        }, 300);
    });
    
    // ============ CART ANIMATIONS ============
    
    // Cart counter pulse animation
    function pulseCartCounter() {
        $('.icon-cart span, .cart-counter').stop(true, false).animate({
            fontSize: '24px'
        }, 200, function() {
            $(this).animate({
                fontSize: '20px'
            }, 200);
        });
    }
    
    // Make pulseCartCounter globally accessible
    window.pulseCartCounter = pulseCartCounter;
    
    // Cart item hover effects
    $(document).on('mouseenter', '.cart-item', function() {
        $(this).stop(true, false).animate({
            backgroundColor: '#f8f9fa',
            transform: 'scale(1.02)'
        }, 300);
    });
    
    $(document).on('mouseleave', '.cart-item', function() {
        $(this).stop(true, false).animate({
            backgroundColor: 'transparent',
            transform: 'scale(1)'
        }, 300);
    });
    
    // Cart toggle animation
    $('.icon-cart, .cart-toggle').on('click', function(e) {
        e.preventDefault();
        $('body').fadeToggle(300, function() {
            $(this).toggleClass('showCart');
        });
    });
    
    // Close cart animation
    $('.close, .close-cart').on('click', function(e) {
        e.preventDefault();
        $('body').fadeToggle(300, function() {
            $(this).removeClass('showCart');
        });
    });
    
    // ============ MODAL/POPUP ANIMATIONS ============
    
    // Modal open animation
    $(document).on('show.bs.modal', '.modal', function() {
        $(this).stop(true, false).animate({
            opacity: 1
        }, 300);
    });
    
    // Modal close animation
    $(document).on('hide.bs.modal', '.modal', function() {
        $(this).stop(true, false).animate({
            opacity: 0
        }, 300);
    });
    
    // ============ DROPDOWN ANIMATIONS ============
    
    // Dropdown toggle with animation
    $(document).on('click', '.dropdown-toggle, [data-toggle="dropdown"]', function() {
        var $menu = $(this).next('.dropdown-menu');
        if ($menu.is(':visible')) {
            $menu.stop(true, false).slideUp(200);
        } else {
            $menu.stop(true, false).slideDown(200);
        }
    });
    
    // ============ NAVIGATION ANIMATIONS ============
    
    // Navigation link hover
    $(document).on('mouseenter', 'nav a, .navbar a, .menu a', function() {
        $(this).stop(true, false).animate({
            color: '#0062F6'
        }, 200);
    });
    
    $(document).on('mouseleave', 'nav a, .navbar a, .menu a', function() {
        $(this).stop(true, false).animate({
            color: ''
        }, 200);
    });
    
    // Mobile menu toggle animation
    $('.menu-toggle, .hamburger, .navbar-toggle').on('click', function() {
        var $menu = $('.navbar-menu, .mobile-menu');
        $menu.stop(true, false).slideToggle(300);
    });
    
    // ============ TABLE ANIMATIONS ============
    
    // Table row hover effects
    $(document).on('mouseenter', 'table tbody tr', function() {
        $(this).stop(true, false).animate({
            backgroundColor: '#f8f9fa',
            transform: 'scale(1.01)'
        }, 200);
    });
    
    $(document).on('mouseleave', 'table tbody tr', function() {
        $(this).stop(true, false).animate({
            backgroundColor: 'transparent',
            transform: 'scale(1)'
        }, 200);
    });
    
    // ============ BADGE/TAG ANIMATIONS ============
    
    // Badge scale on hover
    $(document).on('mouseenter', '.badge, .tag, .label', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1.1)'
        }, 200);
    });
    
    $(document).on('mouseleave', '.badge, .tag, .label', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1)'
        }, 200);
    });
    
    // ============ PROFILE/AVATAR ANIMATIONS ============
    
    // Profile picture hover effects
    $(document).on('mouseenter', '.profile-picture, .avatar, .user-avatar', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1.1) rotate(5deg)',
            boxShadow: '0 5px 20px rgba(0, 0, 0, 0.2)'
        }, 300);
    });
    
    $(document).on('mouseleave', '.profile-picture, .avatar, .user-avatar', function() {
        $(this).stop(true, false).animate({
            transform: 'scale(1) rotate(0deg)',
            boxShadow: 'none'
        }, 300);
    });
    
    // ============ ALERT/NOTIFICATION ANIMATIONS ============
    
    // Auto-hide alerts with animation
    $('.alert, .notification, .toast-message').each(function() {
        $(this).delay(3000).slideUp(400, function() {
            $(this).remove();
        });
    });
    
    // Close button animations for alerts
    $(document).on('click', '.alert-close, .notification-close, .close-notification', function() {
        $(this).closest('.alert, .notification, .toast-message').stop(true, false).slideUp(300, function() {
            $(this).remove();
        });
    });
    
    // ============ SMOOTH SCROLLING ============
    
    // Smooth scroll to anchors
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop(true, false).animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // ============ SCROLL REVEAL ANIMATIONS ============
    
    // Reveal elements on scroll
    function revealOnScroll() {
        $('.scroll-reveal, .fade-on-scroll, .slide-on-scroll').each(function() {
            var $element = $(this);
            var elementPos = $element.offset().top;
            var elementHeight = $element.outerHeight();
            var windowPos = $(window).scrollTop();
            var windowHeight = $(window).height();
            
            if (windowPos + windowHeight > elementPos && !$element.hasClass('revealed')) {
                $element.addClass('revealed');
                $element.stop(true, false).fadeIn(600);
            }
        });
    }
    
    $(window).on('scroll', revealOnScroll);
    revealOnScroll(); // Check on page load
    
    // ============ LAZY LOAD ANIMATIONS ============
    
    // Animate lazy loaded images
    $(document).on('load', 'img[loading="lazy"], img.lazy', function() {
        $(this).stop(true, false).fadeIn(400);
    });
    
    // ============ LOADING STATE ANIMATIONS ============
    
    // Show/hide loading spinner
    window.showLoadingSpinner = function() {
        $('<div class="loading-spinner-overlay"><div class="spinner"></div></div>')
            .appendTo('body')
            .fadeIn(200);
    };
    
    window.hideLoadingSpinner = function() {
        $('.loading-spinner-overlay').stop(true, false).fadeOut(200, function() {
            $(this).remove();
        });
    };
    
    // ============ PAGINATION ANIMATIONS ============
    
    // Pagination link animations
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        
        $('html, body').stop(true, false).animate({
            scrollTop: 0
        }, 400);
        
        $('.content, .product-list, main').stop(true, false).fadeOut(200, function() {
            window.location.href = href;
        });
    });
    
    // ============ SEARCH ANIMATIONS ============
    
    // Search results animation
    $(document).on('input', '.search-input, .search-bar input', function() {
        var $resultsContainer = $('.search-results');
        if ($resultsContainer.length) {
            $resultsContainer.stop(true, false).fadeOut(200);
        }
    });
    
    // ============ DYNAMIC CONTENT ANIMATIONS ============
    
    // Animate newly added elements
    window.animateNewElements = function(selector, animationType = 'slideDown') {
        $(selector).each(function(index) {
            var $el = $(this);
            if (animationType === 'slideDown') {
                $el.hide().delay(index * 100).slideDown(400);
            } else if (animationType === 'fadeIn') {
                $el.hide().delay(index * 100).fadeIn(400);
            } else if (animationType === 'slideUp') {
                $el.hide().delay(index * 100).slideUp(400);
            }
        });
    };
    
    // ============ UTILITY ANIMATION FUNCTIONS ============
    
    // Pulse animation
    window.pulseElement = function(selector, duration = 500) {
        $(selector).stop(true, false).animate({
            opacity: 0.6
        }, duration / 2, function() {
            $(this).animate({
                opacity: 1
            }, duration / 2);
        });
    };
    
    // Shake animation
    window.shakeElement = function(selector, distance = 5, duration = 300) {
        var $el = $(selector);
        var left = parseInt($el.css('left'), 10);
        
        for(var i = 0; i < 5; i++) {
            $el.animate({left: left + distance}, duration / 10)
               .animate({left: left - distance}, duration / 10);
        }
        $el.animate({left: left}, duration / 10);
    };
    
    // Flip animation
    window.flipElement = function(selector, duration = 600) {
        $(selector).stop(true, false).animate({
            rotateY: '360deg'
        }, duration);
    };
    
    // ============ PERFORMANCE OPTIMIZATIONS ============
    
    // Debounce scroll events for better performance
    var scrollTimeout;
    $(window).on('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(revealOnScroll, 100);
    });
    
    // Prevent animation conflicts
    $(document).on('click', 'a, button', function() {
        if ($(this).is(':animated')) {
            return false;
        }
    });
    
});

// Export animation functions to global scope
window.jQuery = jQuery;
window.$ = $;
