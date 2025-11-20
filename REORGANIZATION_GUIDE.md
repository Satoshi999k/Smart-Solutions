# Smart Solutions - File Reorganization Guide

## Quick Start

Follow these steps IN ORDER to reorganize your project files:

### Step 1: Create Backup
```bash
# Double-click this file:
create_backup.bat
```
This creates a timestamped backup of all your files.

### Step 2: Run File Reorganization
```bash
# Double-click this file:
reorganize_files.bat
```
This moves files into the new folder structure:
- CSS files → `assets/css/`
- JS files → `assets/js/`
- Images → `assets/images/`
- Include files → `includes/`

### Step 3: Update File Paths
```bash
# Double-click this file:
update_paths.bat
```
This automatically updates all references to moved files in your PHP and HTML files.

### Step 4: Test Your Website
1. Open your browser
2. Go to: `http://localhost/ITP122/`
3. Test all pages:
   - Homepage loads correctly
   - Images display properly
   - CSS styles are applied
   - JavaScript functions work
   - Cart system functions
   - Login/Register works
   - All product pages load

### Step 5: Manual Checks

#### Check Database Connection
If you get database errors, update `includes/conn.php` path in:
- Any file that shows "Database connection error"

#### Check Upload Paths
If file uploads fail, update upload paths in:
- `register.php` (profile pictures)
- `update_profile.php`
- Any other upload handling files

Update from:
```php
$upload_dir = "uploads/";
```
To:
```php
$upload_dir = "uploads/"; // No change needed - uploads stay in root
```

#### Check CSS Background Images
If background images don't show, check CSS files for:
```css
/* OLD */
background-image: url('../image/bg.jpg');

/* NEW */
background-image: url('../images/bg.jpg');
```

### Step 6: Clean Up (Optional)

After confirming everything works:

1. Delete old empty folders (if any):
   ```bash
   # If image folder is empty
   rmdir image
   ```

2. Keep backup folder for safety (at least 1 week)

3. Delete reorganization scripts (optional):
   - `reorganize_files.bat`
   - `update_paths.bat`
   - `update_paths.ps1`
   - `REORGANIZATION_GUIDE.md`

## Troubleshooting

### Problem: CSS not loading
**Solution:** Check browser console (F12) for 404 errors, verify CSS path is correct:
```html
<link rel="stylesheet" href="assets/css/design.css" />
```

### Problem: Images not showing
**Solution:** Verify image paths changed from `image/` to `assets/images/`:
```html
<img src="assets/images/logo.png" alt="Logo">
```

### Problem: JavaScript not working
**Solution:** Check browser console for JS errors, verify paths:
```html
<script src="assets/js/script.js"></script>
```

### Problem: Include file errors
**Solution:** Update require statements:
```php
require_once 'includes/conn.php';
require_once 'includes/init_cart.php';
```

### Problem: Cart system broken
**Solution:** Check that `init_cart.php` correctly includes `conn.php`:
```php
// In includes/init_cart.php, update:
require_once 'conn.php'; // Change to relative path if needed
```

## New File Structure Reference

```
ITP122/
│
├── assets/                      # All static assets
│   ├── css/
│   │   ├── design.css          # Main design
│   │   ├── style.css           # Additional styles
│   │   └── animations.css      # Animations
│   ├── js/
│   │   ├── script.js           # Main JS
│   │   ├── app.js              # App logic
│   │   ├── search.js           # Search
│   │   ├── search-dynamic.js   # Dynamic search
│   │   └── ajax-cart.js        # Cart AJAX
│   └── images/                  # All images
│       ├── logo.png
│       ├── products/
│       └── etc.
│
├── includes/                    # PHP includes
│   ├── conn.php                # DB connection
│   ├── init_cart.php           # Cart init
│   └── init_cart_system.php    # Cart system
│
├── uploads/                     # User uploads (stays in root)
│   └── profile-pictures/
│
└── [Root Level]                 # Main PHP/HTML files
    ├── index.php
    ├── cart.php
    ├── checkout.php
    ├── product.php
    └── etc.
```

## Benefits

✅ **Organized Structure**: Easy to find files
✅ **Professional**: Follows industry standards
✅ **Maintainable**: Easier to update and debug
✅ **Scalable**: Simple to add new features
✅ **Secure**: Can restrict folder access via .htaccess

## Security Enhancement (Optional)

Create `.htaccess` in `includes/` folder:

```apache
# Prevent direct access to include files
<Files ~ "\.php$">
    Order allow,deny
    Deny from all
</Files>
```

## Rollback Instructions

If something goes wrong:

1. Stop web server
2. Delete current ITP122 folder
3. Restore from your backup folder
4. Restart web server
5. Report the issue

## Support

If you encounter issues:
1. Check this guide's troubleshooting section
2. Review browser console (F12) for errors
3. Check Apache error logs
4. Verify file permissions

## Next Steps After Reorganization

1. ✅ Update documentation
2. ✅ Test all functionality
3. Consider adding:
   - Admin panel in separate folder
   - API folder for future mobile app
   - Separate folder for email templates
   - Config file for settings

---
**Note**: Always keep a backup before making structural changes!
