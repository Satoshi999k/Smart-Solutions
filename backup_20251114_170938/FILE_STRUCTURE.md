# Smart Solutions - New File Structure

## Overview
This document describes the new organized file structure for the Smart Solutions Computer Shop system.

## Directory Structure

```
ITP122/
├── assets/
│   ├── css/
│   │   ├── design.css           # Main design stylesheet
│   │   ├── style.css            # Additional styles
│   │   └── animations.css       # Animation effects
│   ├── js/
│   │   ├── script.js            # Main JavaScript
│   │   ├── app.js               # Application logic
│   │   ├── search.js            # Search functionality
│   │   ├── search-dynamic.js    # Dynamic search
│   │   └── ajax-cart.js         # Cart AJAX functions
│   └── images/
│       └── [all image files]    # All images moved here
│
├── includes/
│   ├── conn.php                 # Database connection
│   ├── init_cart.php            # Cart initialization
│   └── init_cart_system.php     # Cart system setup
│
├── pages/                       # (Optional: Move all .php/.html pages here)
│   ├── index.php
│   ├── product.php
│   └── [other page files]
│
├── admin/                       # (Optional: Admin-only files)
│   ├── view_users.php
│   ├── debug_cart.php
│   └── [other admin files]
│
└── [Root PHP files]             # Main application files
    ├── index.php
    ├── cart.php
    ├── checkout.php
    └── etc.
```

## Path Updates Required

After reorganization, update all file references:

### CSS Links (in all .php and .html files)
**OLD:**
```html
<link rel="stylesheet" href="design.css" />
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="animations.css" />
```

**NEW:**
```html
<link rel="stylesheet" href="assets/css/design.css" />
<link rel="stylesheet" href="assets/css/style.css" />
<link rel="stylesheet" href="assets/css/animations.css" />
```

### JavaScript Links
**OLD:**
```html
<script src="script.js"></script>
<script src="ajax-cart.js"></script>
<script src="search.js"></script>
```

**NEW:**
```html
<script src="assets/js/script.js"></script>
<script src="assets/js/ajax-cart.js"></script>
<script src="assets/js/search.js"></script>
```

### Image References
**OLD:**
```html
<img src="image/logo.png" alt="Logo">
```

**NEW:**
```html
<img src="assets/images/logo.png" alt="Logo">
```

### Include Files (in PHP files)
**OLD:**
```php
require_once 'conn.php';
require_once 'init_cart.php';
```

**NEW:**
```php
require_once 'includes/conn.php';
require_once 'includes/init_cart.php';
```

## Benefits of New Structure

1. **Better Organization**: Clear separation of concerns
2. **Easier Maintenance**: Find files quickly
3. **Professional Standard**: Follows industry best practices
4. **Scalability**: Easy to add new features
5. **Security**: Easier to restrict access to includes/admin folders

## Migration Steps

1. ✅ Create folder structure (run `reorganize_files.bat`)
2. ⏳ Update file paths (automated script will be provided)
3. ⏳ Test all pages
4. ⏳ Update documentation
5. ⏳ Configure .htaccess for security (optional)

## Security Enhancements (Optional)

Create `.htaccess` in `includes/` folder:
```apache
# Deny direct access to include files
Order Deny,Allow
Deny from all
```

## Notes

- Keep backup of original files before reorganizing
- Test thoroughly after migration
- Update any hardcoded paths in database
- Check upload paths if using file uploads
