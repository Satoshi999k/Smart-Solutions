<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="animations.css" />
<meta charset="UTF-8">
<title>SMART SOLUTIONS COMPUTER SHOP</title>
</head>
<body>
<?php
// Start session to check if the user is logged in
session_start();

// Initialize cart from database
require_once 'includes/init_cart.php';

// Database connection (replace with your credentials)
$conn = new mysqli("localhost", "root", "", "smartsolutions");

// Check if logged in
$profile_picture = "image/login-icon.png"; // Default login icon
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Query to get user's profile picture
    $query = "SELECT profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profile_picture'])) {
            $profile_picture = $row['profile_picture']; 
        }
    }
    $stmt->close();
}
$conn->close();
?>
<header>
    <div class="ssheader">
        <div class="logo">
            <img src="image/logo.png" alt="Smart Solutions Logo">
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <div class="search-icon">
                <img src="image/search-icon.png" alt="Search Icon">
            </div>
        </div>
        <a href="location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
        </div>
        <a href="cart.php">
            <div class="cart">
                <img class="cart" src="image/cart-icon.png" alt="cart-icon" style="width: 35px; height: auto;">
                <span class="cart-counter">
                    <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                </span>
            </div>
        </a>

        <style>
            .cart {
                position: relative;
                display: inline-block;
            }

            .cart-counter {
                position: absolute;
                top: 20px; /* Adjust as needed */
                right: -10px; /* Adjust as needed */
                background-color: #6c757d;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                font-size: 14px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            }
        </style>

       <div class="login profile-dropdown" style="position: relative; display: inline-block;">
            <a href="javascript:void(0)" onclick="toggleDropdown()">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                     src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                     alt="login-icon" 
                     style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div id="dropdown-menu" class="dropdown-content" style="display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); z-index: 1; border-radius: 5px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <a href="logout.php" style="color: black; padding: 12px 16px; text-decoration: none; display: block;">Log Out</a>
                <?php endif; ?>
            </div>  
        </div>
        <div class="login-text">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php"></a>
            <?php else: ?>
                <a href="register.php"><p>Login/<br>Sign In</p></a>
            <?php endif; ?>
        </div>
    </div>
    </header>

    <div class="menu">
            <a href="index.php" style="font-weight: bold;">HOME</a>
            <a href="product.php">PRODUCTS</a>
            <a href="products/desktop.php">DESKTOP</a>
            <a href="products/laptop.php">LAPTOP</a>
            <a href="brands.php">BRANDS</a>
    </div>

    <style>
        .carousel-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto 30px auto;
            margin-top: -90px !important;
            margin-bottom: -10px;
            position: relative;
            min-height: 520px;
            box-sizing: border-box;
            padding-left: 25px;
            padding-right: 25px;
            padding-top: 0 !important;
        }
        .carousel-inner {
            width: 100%;
            height: 520px;
            position: relative;
            overflow: hidden;
            border-radius: 0;
            background: transparent;
            box-sizing: border-box;
            padding-top: 0 !important;
            margin-top: 0 !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .carousel-slide img {
            width: auto;
            max-width: 100%;
            height: 100%;
            max-height: 520px;
            object-fit: contain;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-top: auto;
            margin-bottom: auto;
            border-radius: 0;
            background: transparent;
        }
        .carousel-btn-outside {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            background: rgba(255,255,255,0.85);
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            font-size: 2rem;
            color: #222;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s, opacity 0.2s;
            opacity: 0;
            pointer-events: none;
            margin-top: 20px;
        }
        .carousel-container:hover .carousel-btn-outside,
        .carousel-btn-outside:focus {
            opacity: 1;
            pointer-events: auto;
        }
        .carousel-btn-outside:hover {
            background: #1976D2 !important;
            color: #fff !important;
        }
        /* Swiper-style progress bar */
        .carousel-swiper-bar {
            width: 120px;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin: 14px auto 0 auto;
            position: relative;
            overflow: hidden;
        }
        .carousel-swiper-bar-progress {
            height: 100%;
            background: #1976D2;
            border-radius: 2px;
            transition: width 0.4s cubic-bezier(.4,0,.2,1);
            width: 0;
        }
        .carousel-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: -80px;
            width: 100%;
            position: relative;
            z-index: 3;
        }
        .carousel-dots .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #b3c6e6;
            display: inline-block;
            transition: background 0.3s, transform 0.2s;
            cursor: pointer;
            outline: none;
        }
        .carousel-dots .dot.active,
        .carousel-dots .dot:active {
            background: #1976D2 !important;
            transform: scale(1.2);
            box-shadow: 0 0 0 2px #90caf9;
        }
        .carousel-btn-left-outside {
            left: 18px;
        }
        .carousel-btn-right-outside {
            right: 18px;
        }
        @media (max-width: 1400px) {
            .carousel-container {
                max-width: 100vw;
                padding-left: 15px;
                padding-right: 15px;
            }
            .carousel-inner {
                height: 400px;
            }
            .carousel-slide img {
                height: 400px;
            }
        }
        @media (max-width: 900px) {
            .carousel-container {
                max-width: 100vw;
                padding-left: 10px;
                padding-right: 10px;
            }
            .carousel-inner {
                height: 220px;
            }
            .carousel-slide img {
                height: 220px;
            }
        }
        @media (max-width: 600px) {
            .carousel-container {
                max-width: 100vw;
                padding-left: 6px;
                padding-right: 6px;
            }
            .carousel-inner {
                height: 120px;
            }
            .carousel-slide img {
                height: 120px;
            }
            .carousel-btn-outside {
                width: 36px;
                height: 36px;
                font-size: 1.3rem;
            }
            .carousel-btn-left-outside {
                left: 4px;
            }
            .carousel-btn-right-outside {
                right: 4px;
            }
        }
    </style>
    <div class="carousel-container">
        <button class="carousel-btn-outside carousel-btn-left-outside" onclick="prevSlide()">&#10094;</button>
        
        <div class="carousel-inner">
            <div class="carousel-slides" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                <div class="carousel-slide fade active" style="display: block;">
                    <img src="image/features.png" alt="features 1">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features1.jpg" alt="features 2">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features2.jpg" alt="features 3">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/feature3.jpg" alt="features 4">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features4.jpg" alt="features 4">
                </div>
            </div>
            <div class="carousel-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
                <span class="dot" onclick="currentSlide(5)"></span>
            </div>
            <div class="carousel-swiper-bar">
                <div class="carousel-swiper-bar-progress" id="carouselSwiperBar"></div>
            </div>
        </div>
        
        <button class="carousel-btn-outside carousel-btn-right-outside" onclick="nextSlide()">&#10095;</button>
    </div>

    <div class="listProduct"></div>
    
    <div class="product-container">
    <div class="product-box">
        <a href="products/processor.php">
            <img src="image/processor.png" alt="Processor">
        </a>
    </div>
    <div class="product-box">
        <a href="products/motherboard.php">
            <img src="image/motherboard.png" alt="Motherboard">
        </a>
    </div>
    <div class="product-box">
        <a href="products/graphicscard.php">
            <img src="image/graphicscard.png" alt="Graphics Card">
        </a>
    </div>
    <div class="product-box">
        <a href="products/memory.php">
            <img src="image/memory.png" alt="Memory">
        </a>
    </div>
    <div class="product-box">
        <a href="products/ssd.php">
            <img src="image/ssd.png" alt="Solid State Drives">
        </a>
    </div>
    <div class="product-box">
        <a href="products/powersupply.php">
            <img src="image/powersupply.png" alt="Power Supply">
        </a>
    </div>
    <div class="product-box">
        <a href="products/pccase.php">
            <img src="image/pccase.png" alt="PC Case">
        </a>
    </div>
    <div class="product-box">
        <a href="products/laptop.php">
            <img src="image/laptop.png" alt="Laptops">
        </a>
    </div>
    </div>

    <div class="featured-container">
  <div class="desktop-box" style="background-image: url('image/bgdesktop.png');">
    <div class="title-box">
      <p>DESKTOPS</p>
    </div>
    <div class="seemore">
      <a href="products/desktop.php">SEE MORE</a>
    </div>
    <div class="item-container">
      <div class="item">
        <img src="image/desktop1.png" alt="featured-item1">
        <div class="product-info">
          <h4>Intel Core i7-12700 / H610 / 8GB DDR4 /...</h4>
          <p>₱25,195.00</p>
        </div>
       <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="quick-view">BUY NOW</button>
</a>

      </div>
      <div class="item">
        <img src="image/desktop2.png" alt="featured-item2">
        <div class="product-info">
          <h4>Intel Core i3-12100 / H610 / 8GB DDR4 /...</h4>
          <p>₱14,795.00</p>
        </div>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="quick-view">BUY NOW</button>
    </a>

      </div>
    </div>
  </div>
  <div class="laptop-box" style="background-image: url('image/bglaptop.png');">
    <div class="title-box">
      <p>LAPTOPS</p>
    </div>
    <div class="seemore">
      <a href="products/laptop.php">SEE MORE</a>
    </div>
    <div class="item-container">
      <div class="item">
        <img src="image/laptop1.png" alt="featured-item3">
        <div class="product-info">
          <h4>MSI Thin A15 B7UCX-084PH 15.6" F...</h4>
          <p>₱38,995.00</p>
        </div>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="quick-view">BUY NOW</button>
</a>

      </div>
      <div class="item">
        <img src="image/laptop2.png" alt="featured-item4">
        <div class="product-info">
          <h4>Lenovo V15 G4 IRU 15.6" FHD Intel...</h4>
          <p>₱27,995.00</p>
        </div>
       <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="quick-view">BUY NOW</button>
</a>

      </div>
    </div>
  </div>
</div>

    <div class="deals-container">
    <h2><span class="smart">SMART</span> <span class="deals">DEALS</span></h2>
    </div>

    <div class="see-more"><a href="smartdeals.php">SEE MORE</a>
    </div>
    <div class="deals-item-container">
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1,653.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal1.png" alt="Product Image">
            <h4>Team Elite Vulcan TUF 16G...</h4>
            <p class="price">₱1,999.00</p>
            <div class="original-price1">₱3,652.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

        </div>
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱674.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal2.png" alt="Product Image">
            <h4>Team Elite Plus 8GB 1x8 32...</h4>
            <p class="price">₱1,045.00</p>
            <div class="original-price1">₱1,719.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

        </div>
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1659.00</div>
            <div class="deal-brand1">GSKILL</div>
        </div>
            <img src="image/deal3.png" alt="Product Image">
            <h4>G.Skill Ripjaws V 16gb 2x8...</h4>
            <p class="price">₱2,185.00</p>
            <div class="original-price1">₱3,844.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

        </div>
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱622.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal4.png" alt="Product Image">
            <h4>Team Elite 8gb 1x8 1600m...</h4>
            <p class="price">₱1,065.00</p>
            <div class="original-price1">₱1,687.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

        </div>
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱2,745.00</div>
            <div class="deal-brand1">AMD</div>
        </div>
            <img src="image/deal5.png" alt="Product Image">
            <h4>AMD Ryzen 5 Pro 4650G S...</h4>
            <p class="price">₱5,845.00</p>
            <div class="original-price1">₱8,595.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

        </div>
        <div class="deal-item">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1,398.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal6.png" alt="Product Image">
            <h4>Team Elite TForce Delta 2x...</h4>
            <p class="price">₱3,155.00</p>
            <div class="original-price1">₱4,549.00</div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.html' : 'register.php'; ?>">
    <button class="buy-now-deals">BUY NOW</button>
</a>

                </div>
            </div>
        </div>

        <footer class="footer">
        <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="paymentfaq.php">Payment FAQs</a></li>
            <li><a href="ret&ref.php">Return and Refunds</a></li>
        </ul>
        </div>
        <div class="footer-col">
            <h3>Company</h3>
            <ul>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact_us.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Links</h3>
            <ul>
                <li><a href="corporate.php">SmartSolutions Corporate</a></li>
                <li><a href="https://web.facebook.com/groups/1581265395842396" target="_blank">SmartSolutions Community</a></li>
            </ul>
        </div>
        <div class="footer-col footer-logo">
            <h4>Follow Us</h4>
            <img src="image/logo.png" alt="Company Logo">
            <div class="social-media">
                <a href="https://web.facebook.com/smartsolutionscomputershop.ph" target="_blank">
                    <img src="image/facebook.png" alt="Facebook">
                </a>
                <a href="https://www.instagram.com/smartsolutions.ph/" target="_blank">
                    <img src="image/instagram.png" alt="Instagram">
                </a>
                <a href="https://www.tiktok.com/@smrtsolutioncomputershop" target="_blank">
                    <img src="image/tiktok.png" alt="Tiktok">
                </a>
                <a href="https://www.linkedin.com/in/smart-solutions-40b512339/" target="_blank">
                    <img src="image/linkedin.png" alt="LinkedIn">
                </a>
                <a href="https://www.youtube.com/@SmartSolutions-o4j/" target="_blank">
                    <img src="image/youtube.png" alt="YouTube">
                </a>
            </div>
        </div>
    </footer>
    <div class="copyright">
        &copy; 2024 SmartSolutions. All rights reserved.
    </div>
    <script>
    // Add JavaScript to toggle dropdown visibility
    function toggleDropdown() {
        var dropdownMenu = document.getElementById("dropdown-menu");
        // Toggle the visibility of the dropdown menu
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    }

    // Close the dropdown if the user clicks anywhere outside of it
    window.onclick = function(event) {
        var dropdownMenu = document.getElementById("dropdown-menu");
        if (!event.target.matches('.profile-dropdown, .profile-dropdown *')) {
            dropdownMenu.style.display = 'none';  // Hide the dropdown menu if clicked outside
        }
    };

    // Carousel functionality
    let slideIndex = 1;
    let autoSlideTimer;

    function showSlide(n) {
        const slides = document.getElementsByClassName("carousel-slide");
        const dots = document.getElementsByClassName("dot");
        const swiperBar = document.getElementById("carouselSwiperBar");
        const totalSlides = slides.length;
        if (n > totalSlides) slideIndex = 1;
        if (n < 1) slideIndex = totalSlides;
        for (let i = 0; i < totalSlides; i++) {
            slides[i].classList.remove("active");
            slides[i].style.display = "none";
        }
        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove("active");
        }
        slides[slideIndex - 1].classList.add("active");
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].classList.add("active");
        // Swiper bar update
        if (swiperBar) {
            swiperBar.style.width = ((slideIndex) / totalSlides * 100) + "%";
        }
    }

    function nextSlide() {
        clearTimeout(autoSlideTimer);
        slideIndex++;
        showSlide(slideIndex);
        startAutoSlide();
    }

    function prevSlide() {
        clearTimeout(autoSlideTimer);
        slideIndex--;
        showSlide(slideIndex);
        startAutoSlide();
    }

    function currentSlide(n) {
        clearTimeout(autoSlideTimer);
        slideIndex = n;
        showSlide(slideIndex);
        startAutoSlide();
    }

    function autoSlide() {
        slideIndex++;
        showSlide(slideIndex);
        autoSlideTimer = setTimeout(autoSlide, 5000);
    }

    function startAutoSlide() {
    clearTimeout(autoSlideTimer);
    autoSlideTimer = setTimeout(autoSlide, 5000); // Change slide every 5 seconds
    }

    // Initialize carousel on page load
    document.addEventListener('DOMContentLoaded', function() {
        showSlide(slideIndex);
        startAutoSlide();
    });
</script>
<script src="search.js"></script>
<script src="app.js"></script>
<script src="ajax-cart.js"></script>
</body>
</html>
