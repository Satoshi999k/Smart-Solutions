<?php
session_start();

// Clear the cart from the session
unset($_SESSION['cart']);

// Redirect to thankyou.html
header("Location: thankyou.html");
exit; //Important: Stop further execution after header redirect
?>