<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" href="image/smartsolutionslogo.jpg" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/design.css" />
<link rel="stylesheet" href="css/animations.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta charset="UTF-8">
<title>SMART SOLUTIONS COMPUTER SHOP</title>
<?php
// Display alert if one is set
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    echo "<script>
        window.addEventListener('load', function() {
            Swal.fire({
                title: '" . addslashes($alert['title']) . "',
                text: '" . addslashes($alert['message']) . "',
                icon: '" . $alert['type'] . "',
                confirmButtonColor: '#0062F6',
                confirmButtonText: 'OK'
            });
        });
    </script>";
    unset($_SESSION['alert']);
}
?>
<style>
/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .ssheader {
        margin-left: 10px;
        flex-wrap: wrap;
        justify-content: space-between;
        width: 100%;
    }
    
    .logo img {
        height: 60px;
        width: 70px;
        margin-left: 0;
        margin-right: 10px;
    }
    
    .search-bar {
        order: 3;
        width: 100%;
        margin: 10px 0;
    }
    
    .search-bar input {
        width: 100%;
        max-width: 100%;
        padding: 8px 35px 8px 15px;
    }
    
    .search-icon {
        right: 15px !important;
    }
    
    .location, .track, .cart, .login {
        width: auto;
        margin: 0 5px;
    }
    
    .location img, .track img {
        width: 25px;
        height: auto;
    }
    
    .cart img {
        width: 28px !important;
    }
    
    .cart-counter {
        top: 15px !important;
        right: -8px !important;
        width: 18px;
        height: 18px;
        font-size: 12px;
    }
    
    .login img {
        width: 30px !important;
        height: 30px !important;
    }
    
    .login-text {
        display: none;
    }
    
    .menu {
        flex-wrap: wrap;
        justify-content: center;
        padding: 10px 5px;
    }
    
    .menu a {
        font-size: 12px;
        padding: 8px 10px;
        margin: 2px;
    }
    
    .product-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 10px;
    }
    
    .featured-container {
        flex-direction: column;
        padding: 10px;
    }
    
    .desktop-box, .laptop-box {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .item-container {
        flex-direction: column;
        align-items: center;
    }
    
    .item {
        width: 100%;
        max-width: 300px;
        margin-bottom: 15px;
    }
    
    .deals-item-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 10px;
    }
    
    .deal-item h4 {
        font-size: 12px;
    }
    
    .deal-item .price {
        font-size: 14px;
    }
    
    .footer {
        flex-direction: column;
        padding: 20px 10px;
        text-align: center;
    }
    
    .footer-col {
        margin-bottom: 20px;
        width: 100%;
    }
}

@media (max-width: 480px) {
    .logo img {
        height: 50px;
        width: 60px;
    }
    
    .menu a {
        font-size: 10px;
        padding: 6px 8px;
    }
    
    .product-container {
        grid-template-columns: 1fr;
    }
    
    .deals-item-container {
        grid-template-columns: 1fr;
    }
    
    .deals-container h2 {
        font-size: 20px;
    }
}

/* Menu animation from top */
@keyframes slideDownMenu {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#main-menu {
    animation: slideDownMenu 0.6s ease-out 0.3s forwards;
    opacity: 0;
}

#main-menu a {
    transition: all 0.3s ease;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <img class="search-icon" src="image/search-icon.png" alt="Search" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <a href="pages/location.php">
            <div class="location">
                <img class="location" src="image/location-icon.png" alt="location-icon">
            </div>
        </a>
        <div class="track">
            <a href="pages/track.php"><img class="track" src="image/track-icon.png" alt="track-icon"></a>
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

        <!-- Modern Profile Dropdown CSS -->
        <style>
            .profile-dropdown { position: relative; display: inline-block; }
            
            .dropdown-content { display: none; position: absolute; top: 110%; right: 0; background: white; border-radius: 8px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border: 1px solid #e0e0e0; min-width: 200px; z-index: 1000; }
            
            .dropdown-content a { display: flex; align-items: center; padding: 12px 16px; color: #333; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s ease; border-left: 3px solid transparent; }
            
            .dropdown-content a:hover { background: #f5f5f5; color: #0062F6; border-left-color: #0062F6; }
            
            .dropdown-content a .material-icons { font-size: 18px; margin-right: 12px; display: flex; align-items: center; }
            
            .profile-dropdown.active .dropdown-content { display: block; animation: slideDown 0.25s ease-out; }
            
            @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        </style>

       <div class="login profile-dropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown(event)">
                <!-- Check if user is logged in, if yes show profile picture, else show login icon -->
                <img class="login" 
                    src="<?php echo isset($_SESSION['user_id']) ? $profile_picture : 'image/login-icon.png'; ?>" 
                    alt="login-icon" 
                    style="border-radius: <?php echo isset($_SESSION['user_id']) ? '50%' : '0'; ?>; 
                            width: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>; 
                            height: <?php echo isset($_SESSION['user_id']) ? '40px' : '30px'; ?>;">
            </a>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/profile.php"><span class="material-icons">person</span>View Profile</a>
                    <a href="user/edit-profile.php"><span class="material-icons">edit</span>Edit Profile</a>
                    <a href="user/logout.php"><span class="material-icons">logout</span>Log Out</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="login-text">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/profile.php"></a>
                <?php else: ?>
                    <a href="user/register.php"><p>Login/<br>Sign In</p></a>
                <?php endif; ?>
            </div>
    </div>
    </header>

    <div class="menu" id="main-menu">
            <a href="index.php" style="font-weight: bold;">HOME</a>
            <a href="pages/product.php">PRODUCTS</a>
            <a href="products/desktop.php">DESKTOP</a>
            <a href="products/laptop.php">LAPTOP</a>
            <a href="pages/brands.php">BRANDS</a>
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
            margin-top: 10px;
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
                    <img src="image/features23.jpg" alt="features 3">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/feature3.jpg" alt="features 4">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features4.jpg" alt="features 5">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features3.png" alt="features 6">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features2.png" alt="features 7">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features4.png" alt="features 8">
                </div>
                <div class="carousel-slide fade">
                    <img src="image/features1.png" alt="features 9">
                </div>
            </div>
            <div class="carousel-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
                <span class="dot" onclick="currentSlide(4)"></span>
                <span class="dot" onclick="currentSlide(5)"></span>
                <span class="dot" onclick="currentSlide(6)"></span>
                <span class="dot" onclick="currentSlide(7)"></span>
                <span class="dot" onclick="currentSlide(8)"></span>
                <span class="dot" onclick="currentSlide(9)"></span>
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
       <button class="quick-view" onclick="buyNow(1, 'Intel Core i7-12700 / H610 / 8GB DDR4', '25195.00', 'image/desktop1.png')">BUY NOW</button>

      </div>
      <div class="item">
        <img src="image/desktop2.png" alt="featured-item2">
        <div class="product-info">
          <h4>Intel Core i3-12100 / H610 / 8GB DDR4 /...</h4>
          <p>₱14,795.00</p>
        </div>
        <button class="quick-view" onclick="buyNow(2, 'Intel Core i3-12100 / H610 / 8GB DDR4', '14795.00', 'image/desktop2.png')">BUY NOW</button>

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
        <button class="quick-view" onclick="buyNow(3, 'MSI Thin A15 B7UCX-084PH 15.6', '38995.00', 'image/msithin.png')">BUY NOW</button>

      </div>
      <div class="item">
        <img src="image/laptop2.png" alt="featured-item4">
        <div class="product-info">
          <h4>Lenovo V15 G4 IRU 15.6" FHD Intel...</h4>
          <p>₱27,995.00</p>
        </div>
       <button class="quick-view" onclick="buyNow(4, 'Lenovo V15 G4 IRU 15.6 FHD Intel', '27995.00', 'image/ideapad.png')">BUY NOW</button>

      </div>
    </div>
  </div>
</div>

    <div class="deals-container animated fadeInUp">
    <h2><span class="smart">SMART</span> <span class="deals">DEALS</span></h2>
    </div>

    <div class="see-more animated slideDown"><a href="smartdeals.php">SEE MORE</a>
    </div>
    <div class="deals-item-container">
        <div class="deal-item animated scaleIn">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1,653.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal1.png" alt="Product Image">
            <h4>Team Elite Vulcan TUF 16G...</h4>
            <p class="price">₱1,999.00</p>
            <div class="original-price1">₱3,652.00</div>
            <button class="buy-now-deals" onclick="buyNow(5, 'Team Elite Vulcan TUF 16G', '1999.00', 'image/deal1.png')">BUY NOW</button>

        </div>
        <div class="deal-item animated scaleIn" style="animation-delay: 0.1s">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱674.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal2.png" alt="Product Image">
            <h4>Team Elite Plus 8GB 1x8 32...</h4>
            <p class="price">₱1,045.00</p>
            <div class="original-price1">₱1,719.00</div>
            <button class="buy-now-deals" onclick="buyNow(6, 'Team Elite Plus 8GB 1x8 32', '1045.00', 'image/deal2.png')">BUY NOW</button>

        </div>
        <div class="deal-item animated scaleIn" style="animation-delay: 0.2s">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1659.00</div>
            <div class="deal-brand1">GSKILL</div>
        </div>
            <img src="image/deal3.png" alt="Product Image">
            <h4>G.Skill Ripjaws V 16gb 2x8...</h4>
            <p class="price">₱2,185.00</p>
            <div class="original-price1">₱3,844.00</div>
            <button class="buy-now-deals" onclick="buyNow(7, 'G.Skill Ripjaws V 16gb 2x8', '2185.00', 'image/deal3.png')">BUY NOW</button>

        </div>
        <div class="deal-item animated scaleIn" style="animation-delay: 0.3s">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱622.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal4.png" alt="Product Image">
            <h4>Team Elite 8gb 1x8 1600m...</h4>
            <p class="price">₱1,065.00</p>
            <div class="original-price1">₱1,687.00</div>
            <button class="buy-now-deals" onclick="buyNow(8, 'Team Elite 8gb 1x8 1600m', '1065.00', 'image/deal4.png')">BUY NOW</button>

        </div>
        <div class="deal-item animated scaleIn" style="animation-delay: 0.4s">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱2,745.00</div>
            <div class="deal-brand1">AMD</div>
        </div>
            <img src="image/deal5.png" alt="Product Image">
            <h4>AMD Ryzen 5 Pro 4650G S...</h4>
            <p class="price">₱5,845.00</p>
            <div class="original-price1">₱8,595.00</div>
            <button class="buy-now-deals" onclick="buyNow(9, 'AMD Ryzen 5 Pro 4650G S', '5845.00', 'image/deal5.png')">BUY NOW</button>

        </div>
        <div class="deal-item animated scaleIn" style="animation-delay: 0.5s">
            <div class="deal-header1">
            <div class="deal-badge1">SAVE ₱1,398.00</div>
            <div class="deal-brand1">TEAM ELITE</div>
        </div>
            <img src="image/deal6.png" alt="Product Image">
            <h4>Team Elite TForce Delta 2x...</h4>
            <p class="price">₱3,155.00</p>
            <div class="original-price1">₱4,549.00</div>
            <button class="buy-now-deals" onclick="buyNow(10, 'Team Elite TForce Delta 2x', '3155.00', 'image/deal6.png')">BUY NOW</button>

                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <style>
            @keyframes waveGradient {
                0% {
                    background: linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%);
                    background-size: 400% 400%;
                    background-position: 0% 50%;
                }
                25% {
                    background-position: 50% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
                75% {
                    background-position: 50% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }
            
            .why-choose-us-animated {
                background: linear-gradient(45deg, #007BFF 0%, #0056b3 25%, #003f87 50%, #0056b3 75%, #007BFF 100%);
                background-size: 400% 400%;
                animation: waveGradient 8s ease-in-out infinite;
            }
        </style>
        <div class="why-choose-us-section why-choose-us-animated" style="padding: 50px 20px; text-align: center; margin: 40px 0; position: relative; overflow: hidden;">
            <h2 style="font-size: 2.2em; color: white; margin-bottom: 10px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Why Choose Us</h2>
            <p class="section-subtitle" style="font-size: 1em; color: rgba(255, 255, 255, 0.95); margin-bottom: 40px;">Experience the difference with our premium service</p>
            <div class="features-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; max-width: 1100px; margin: 0 auto; position: relative; z-index: 2;">
                <div class="feature-card" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); padding: 28px 20px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                    <div class="feature-icon" style="font-size: 2em; margin-bottom: 12px; display: block; animation: float 5s ease-in-out infinite; color: white;"><i class="material-icons" style="font-size: 2em;">local_shipping</i></div>
                    <h3 style="font-size: 1.1em; margin-bottom: 10px; font-weight: bold; color: white;">Lightning Fast Delivery</h3>
                    <p style="font-size: 0.85em; line-height: 1.5; color: rgba(255, 255, 255, 0.9);">Get your orders delivered within 48 hours with our express shipping service.</p>
                </div>
                <div class="feature-card" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); padding: 28px 20px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                    <div class="feature-icon" style="font-size: 2em; margin-bottom: 12px; display: block; animation: float 5s ease-in-out infinite 0.7s; color: white;"><i class="material-icons" style="font-size: 2em;">verified_user</i></div>
                    <h3 style="font-size: 1.1em; margin-bottom: 10px; font-weight: bold; color: white;">Secure Payments</h3>
                    <p style="font-size: 0.85em; line-height: 1.5; color: rgba(255, 255, 255, 0.9);">Shop with confidence using our encrypted and protected payment gateway.</p>
                </div>
                <div class="feature-card" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); padding: 28px 20px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden;">
                    <div class="feature-icon" style="font-size: 2em; margin-bottom: 12px; display: block; animation: float 5s ease-in-out infinite 1.4s; color: white;"><i class="material-icons" style="font-size: 2em;">assignment_return</i></div>
                    <h3 style="font-size: 1.1em; margin-bottom: 10px; font-weight: bold; color: white;">Easy Returns</h3>
                    <p style="font-size: 0.85em; line-height: 1.5; color: rgba(255, 255, 255, 0.9);">Not satisfied? Return within 30 days for a full refund, no questions asked.</p>
                </div>
            </div>
        </div>
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            
            .feature-card:hover {
                transform: translateY(-15px) !important;
                box-shadow: 0 25px 50px rgba(0, 123, 255, 0.3) !important;
            }
        </style>

        <!-- What Our Customers Say Section -->
        <div style="padding: 50px 20px; text-align: left; margin: 40px 0; background: transparent;">
            <h2 style="font-size: 2.2em; color: #333; margin-bottom: 10px; font-weight: bold; text-align: center;">What Our Customers Say</h2>
            <p style="font-size: 1em; color: #666; margin-bottom: 40px; text-align: center;">Real stories from shoppers who found their perfect tech with SmartSolutions</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; max-width: 1100px; margin: 0 auto;">
                <!-- Testimonial 1 -->
                <div class="testimonial-card" style="background: white; padding: 12px 10px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; text-align: left; display: flex; flex-direction: column; min-height: auto; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px;">
                        <div style="color: #FFB800; font-size: 1.1em; letter-spacing: 2px;">★★★★★</div>
                        <div style="color: #333; font-weight: bold; font-size: 0.95em;">5.0</div>
                    </div>
                    <p style="font-size: 0.95em; line-height: 1.7; color: #555; margin-bottom: 12px; flex-grow: 1;">The laptop I ordered arrived perfectly packaged and within 2 days. The quality exceeded my expectations and the customer service was incredibly helpful. Definitely my go-to store now!</p>
                    <div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: auto;">
                        <p style="font-weight: 600; color: #333; margin: 0; font-size: 0.95em;">Jhon Rey Dominise</p>
                        <p style="font-size: 0.85em; color: #888; margin: 2px 0;">Professional • Davao City</p>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="testimonial-card" style="background: white; padding: 12px 10px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; text-align: left; display: flex; flex-direction: column; min-height: auto; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px;">
                        <div style="color: #FFB800; font-size: 1.1em; letter-spacing: 2px;">★★★★★</div>
                        <div style="color: #333; font-weight: bold; font-size: 0.95em;">5.0</div>
                    </div>
                    <p style="font-size: 0.95em; line-height: 1.7; color: #555; margin-bottom: 12px; flex-grow: 1;">I've been a customer for over a year now. SmartSolutions has the best prices and their technical support team always goes the extra mile. Highly recommended!</p>
                    <div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: auto;">
                        <p style="font-weight: 600; color: #333; margin: 0; font-size: 0.95em;">Mark Foster</p>
                        <p style="font-size: 0.85em; color: #888; margin: 2px 0;">Student • Tagum City</p>
                    </div>
                </div>
                <!-- Testimonial 3 -->
                <div class="testimonial-card" style="background: white; padding: 12px 10px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; text-align: left; display: flex; flex-direction: column; min-height: auto; position: relative;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px;">
                        <div style="color: #FFB800; font-size: 1.1em; letter-spacing: 2px;">★★★★★</div>
                        <div style="color: #333; font-weight: bold; font-size: 0.95em;">4.9</div>
                    </div>
                    <p style="font-size: 0.95em; line-height: 1.7; color: #555; margin-bottom: 12px; flex-grow: 1;">Best gaming laptop setup I could get for the price. The build quality is excellent and I love the warranty coverage. Will definitely be back for upgrades!</p>
                    <div style="border-top: 1px solid #eee; padding-top: 8px; margin-top: auto;">
                        <p style="font-weight: 600; color: #333; margin: 0; font-size: 0.95em;">Ryan Mangaliwan</p>
                        <p style="font-size: 0.85em; color: #888; margin: 2px 0;">Content Creator • Mati City</p>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
        <div class="footer-col">
        <h3>Customer Service</h3>
        <ul>
            <li><a href="pages/paymentfaq.php">Payment FAQs</a></li>
            <li><a href="pages/ret&ref.php">Return and Refunds</a></li>
        </ul>
        </div>
        <div class="footer-col">
            <h3>Company</h3>
            <ul>
                <li><a href="pages/about_us.php">About Us</a></li>
                <li><a href="pages/contact_us.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Links</h3>
            <ul>
                <li><a href="pages/corporate.php">SmartSolutions Corporate</a></li>
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
        // Modern dropdown toggle function
        function toggleDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            const profileDropdown = document.querySelector('.profile-dropdown');
            profileDropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (profileDropdown && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('active');
            }
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const profileDropdown = document.querySelector('.profile-dropdown');
                if (profileDropdown) {
                    profileDropdown.classList.remove('active');
                }
            }
        });
    </script>

    <script>
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
<script src="js/search.js"></script>

<script src="js/ajax-cart.js?v=2024-11-18-9"></script>
<script>
$(document).ready(function() {
    // Add hover animation to deal items
    $('.deal-item').hover(function() {
        $(this).addClass('pulse');
    }, function() {
        $(this).removeClass('pulse');
    });
    
    // Add hover effect to deal badges
    $('.deal-badge1').hover(function() {
        $(this).animate({fontSize: '13px'}, 200);
    }, function() {
        $(this).animate({fontSize: '11px'}, 200);
    });
    
    // Animate buy now buttons on click
    $('.buy-now-deals').on('mousedown', function() {
        $(this).addClass('buttonClick');
    }).on('mouseup', function() {
        $(this).removeClass('buttonClick');
    });
});

// Buy Now function for featured products and deals
function buyNow(productId, productName, productPrice, productImage) {
    // Check if user is logged in
    const userLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    if (!userLoggedIn) {
        window.location.href = 'user/register.php';
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('product_price', productPrice);
    formData.append('product_image', productImage);
    formData.append('quantity', 1);
    
    // Send data to set_buynow_product.php
    fetch('set_buynow_product.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to checkout
            window.location.href = 'checkout.php';
        } else {
            alert('Error: ' + (data.message || 'Unable to process buy now'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
<script src="../js/header-animation.js"></script>
<script src="../js/search.js"></script>

<!-- Smooth Page Animations & Transitions -->
<script>
$(document).ready(function() {
    // Fade in entire page
    $('body').fadeIn(600);
    
    // Testimonial cards staggered fade-in
    $('.testimonial-card').each(function(index) {
        $(this).hide().delay(index * 200).fadeIn(600);
    });
    
    // Testimonial cards hover effect - lift up and shadow
    $('.testimonial-card').hover(function() {
        $(this).stop().animate({
            marginTop: '-10px',
            boxShadow: '0 15px 40px rgba(0, 0, 0, 0.3)'
        }, 300);
    }, function() {
        $(this).stop().animate({
            marginTop: '0px',
            boxShadow: '0 4px 15px rgba(0, 0, 0, 0.1)'
        }, 300);
    });
    
    // Feature cards fade in
    $('.feature-card').each(function(index) {
        $(this).hide().delay(index * 100).fadeIn(500);
    });
    
    // Feature cards hover scale
    $('.feature-card').hover(function() {
        $(this).stop().animate({
            transform: 'scale(1.05)'
        }, 250);
    }, function() {
        $(this).stop().animate({
            transform: 'scale(1)'
        }, 250);
    });
    
    // Product cards animation
    $('.product-card').each(function(index) {
        $(this).hide().delay(index * 80).fadeIn(500);
    });
    
    // Smooth scroll animation for anchor links
    $('a[href*="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
    
    // Section visibility fade-in on scroll
    function checkScroll() {
        $('.why-choose-us-section, .feature-card, .product-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                if (!$(this).hasClass('fade-in-visible')) {
                    $(this).addClass('fade-in-visible');
                    $(this).animate({opacity: 1}, 600);
                }
            }
        });
    }
    
    $(window).on('scroll', checkScroll);
    checkScroll();
});
</script>

</body>
</html>