# FOLLOW THESE STEPS TO REORGANIZE YOUR PROJECT

## Step 1: Double-click "REORGANIZE.bat"
This file is in your ITP122 folder.

When it opens, you'll see a menu:
```
[1] Complete Reorganization (Recommended)
[2] Create Backup Only  
[3] View Documentation
[4] Exit
```

## Step 2: Press "1" and hit Enter
This will:
- ✅ Create a backup automatically
- ✅ Create folders (assets/css, assets/js, assets/images, includes)
- ✅ Move all files to new locations
- ✅ Update all paths in your PHP/HTML files

## Step 3: Wait for it to finish
You'll see messages like:
```
[1/4] Creating backup...
[2/4] Creating folder structure...
[3/4] Moving files...
[4/4] Updating file paths...
```

## Step 4: Test your website
Open your browser and go to:
```
http://localhost/ITP122/
```

Check:
- ✅ Homepage loads
- ✅ Images display
- ✅ CSS styles work
- ✅ Navigation works
- ✅ Cart works
- ✅ Login/Register works

## That's it!

Your files will now be organized like:
```
ITP122/
├── assets/
│   ├── css/        (all CSS files)
│   ├── js/         (all JS files)
│   └── images/     (all images)
├── includes/       (conn.php, init_cart.php)
└── [your PHP/HTML files stay here]
```

## If something goes wrong:
1. Find the backup folder (starts with "backup_")
2. Copy everything from backup back to ITP122
3. Try again or ask for help

---
**Ready? Go to Step 1 now!**
