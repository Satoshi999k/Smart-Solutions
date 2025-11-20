SmartSolutions — Database import and admin setup

This project uses a local MySQL database named `smartsolutions`.

Files created/updated:
- `create_database.sql` — Creates the `smartsolutions` database and tables: `users`, `user_form`, `orders`, and `products` (with sample products).

How to import (Windows, using bash.exe):

1) Using the MySQL CLI:

```bash
# Run from anywhere; provide your MySQL root password when prompted
mysql -u root -p < "d:/xampp/htdocs/ITP122/create_database.sql"
```

2) Using phpMyAdmin:
- Open phpMyAdmin (usually http://localhost/phpmyadmin).
- Click Import, choose the `create_database.sql` file and run it.

Create an admin user (securely)

The SQL file contains a commented INSERT statement for an admin user but you need a bcrypt password hash. Generate the hash in PHP and then run an INSERT with that hash. Example PHP snippet:

```php
<?php
// Generate a password hash for the admin account
$plain = 'YourStrongAdminPassword';
$hash = password_hash($plain, PASSWORD_BCRYPT);
echo "Password hash: $hash\n";
// Copy the hash and run the SQL (either via phpMyAdmin or mysql CLI):
// INSERT INTO `users` (email, password, first_name, last_name) VALUES ('admin@example.com', '<PASTE_HASH>', 'Admin', 'User');
```

Run the PHP snippet from the command line (if you have PHP in PATH):

```bash
php -r "echo password_hash('YourStrongAdminPassword', PASSWORD_BCRYPT) . PHP_EOL;"
```

Then paste the printed hash into the SQL INSERT statement and run it.

Notes and suggestions
- Product data currently exists in PHP arrays in the project; the `products` table is optional and populated with a few sample rows. You can expand or replace these with the full product list.
- `orders.customer_details` and `orders.order_details` are JSON columns. `process_order.php` stores JSON there; verify `process_order.php` uses proper bind types for prepared statements.
- If you want, I can:
  - Add the full product catalog from the PHP arrays into the `products` table.
  - Add migration PHP scripts to move PHP-array products to the DB.
  - Update `process_order.php` to use prepared statements with correct types (if any binding type mismatches exist).

If you want me to proceed with any of the optional items above, tell me which one and I'll implement it.
