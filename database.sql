-- SQL schema for SmartSolutions project
-- Creates database and tables used by the PHP files in this workspace.
-- Import with phpMyAdmin or: mysql -u root -p < create_database.sql

DROP DATABASE IF EXISTS `smartsolutions`;
CREATE DATABASE `smartsolutions` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `smartsolutions`;

-- Users table (used by register.php, login.php, update-profile.php, edit-profile.php, etc.)
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `address` TEXT,
  `phone_number` VARCHAR(50),
  `postal_code` VARCHAR(50),
  `profile_picture` VARCHAR(255) DEFAULT 'image/default-profile.png',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Legacy / alternate table referenced in some files (`update_profile.php` updates `user_form`)
CREATE TABLE `user_form` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Orders table (used by process_order.php)
-- customer_details and order_details stored as JSON for flexibility
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_details` JSON DEFAULT NULL,
  `order_details` JSON DEFAULT NULL,
  `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional: sample admin user (UNCOMMENT to insert). Password must be a password_hash() value generated in PHP.
-- INSERT INTO `users` (email, password, first_name, last_name) VALUES
-- ('admin@example.com', '$2y$10$REPLACE_WITH_PHP_PASSWORD_HASH', 'Admin', 'User');

-- Notes:
-- 1) Many product lists in the code are stored as PHP arrays; if you later want to persist products in DB, add a `products` table and migrate arrays.
-- 2) Some PHP code uses slightly different table names/column sets (e.g. `user_form`) — this SQL creates a compatible `user_form` table to avoid runtime errors.
-- 3) If you use the mysql CLI on Windows with the provided shell, run:
--    mysql -u root -p < "d:/xampp/htdocs/ITP122/create_database.sql"
-- 4) If using phpMyAdmin, import the file and run it.

-- PRODUCTS TABLE (optional but helpful):
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `stock` INT DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample products (based on product arrays found in the PHP files). Edit as needed.
INSERT INTO `products` (`id`,`name`,`price`,`image`,`category`,`stock`,`description`) VALUES
(1,'Core i7 12700 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W',25195.00,'image/desktop1.png','desktop',10,'Prebuilt desktop with Intel Core i7 12700'),
(2,'Core i3 12100 / H610 / 8GB DDR4 / 256GB SSD / PC Case M-ATX with 700W',14795.00,'image/desktop2.png','desktop',8,'Prebuilt desktop with Intel Core i3 12100'),
(3,'MSI Thin A15 B7UCX-084PH 15.6 / FHD 144Hz AMD RYZEN 5 7535HS/8GB/512GBSSD/RTX 2050 4GB/WIN11 Laptop',38995.00,'image/laptop1.png','laptop',5,'MSI thin laptop with Ryzen 5'),
(4,'Lenovo V15 G4 IRU 15.6 / FHD Intel Core i5- 1335U/8GB DDR4/512GB M.2 SSD Laptop MN',29495.00,'image/laptop2.png','laptop',6,'Lenovo V15 with Intel Core i5'),
(5,'Team Elite Vulcan TUF 16gb 2x8 3200mhz Ddr4 Gaming Memory',1999.00,'image/deal1.png','memory',25,'16GB DDR4 RAM kit'),
(6,'Team Elite Plus 8gb 1x8 3200Mhz Black Gold Ddr4 Memory',1045.00,'image/deal2.png','memory',30,'8GB DDR4 RAM stick');

-- SHOPPING CART TABLE (stores cart items for logged-in users)
CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Helpful INSERT snippet to create an admin user (use the PHP snippet in README.md to generate the hash):
-- INSERT INTO `users` (email, password, first_name, last_name) VALUES ('admin@example.com','<PASTE_PASSWORD_HASH_HERE>','Admin','User');

-- End of file
