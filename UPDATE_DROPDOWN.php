<?php
// Script to update dropdown menu in all PHP files

$directory = __DIR__;
$files = glob($directory . '/*.php');

$old_pattern = '/<div id="dropdown-menu" class="dropdown-content"[^>]*>[\s\S]*?<\?php if \(isset\(\$_SESSION\[\'user_id\'\]\)\): \?>[\s\S]*?<a href="logout\.php"[^>]*>Log Out<\/a>[\s\S]*?<\?php endif; \?>[\s\S]*?<\/div>/';

$new_dropdown = '<div id="dropdown-menu" class="dropdown-content" style="display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); z-index: 1; border-radius: 5px;">
                <?php if (isset($_SESSION[\'user_id\'])): ?>
                    <a href="user/edit-profile.php" style="color: black; padding: 12px 16px; text-decoration: none; display: block;">Edit Profile</a>
                    <a href="user/logout.php" style="color: black; padding: 12px 16px; text-decoration: none; display: block;">Log Out</a>
                <?php endif; ?>
            </div>';

$updated = 0;
$skipped = 0;

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip this script and some system files
    if (in_array($filename, ['UPDATE_DROPDOWN.php', 'conn.php', 'logout.php', 'edit-profile.php', 'update-profile.php', 'update_profile.php'])) {
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check if file has dropdown-menu
    if (strpos($content, 'dropdown-menu') !== false) {
        // Try to replace the dropdown
        $new_content = preg_replace($old_pattern, $new_dropdown, $content);
        
        if ($new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "✓ Updated: $filename\n";
            $updated++;
        } else {
            // Try alternative approach - look for the specific pattern
            if (preg_match('/<a href="logout\.php"[^>]*>Log Out<\/a>/', $content) && 
                !preg_match('/<a href="edit-profile\.php"[^>]*>Edit Profile<\/a>/', $content)) {
                
                // Add Edit Profile link before Log Out
                $new_content = preg_replace(
                    '/(<\?php if \(isset\(\$_SESSION\[\'user_id\'\]\)\): \?>[\s\S]*?)(<a href="logout\.php")/i',
                    '$1<a href="user/edit-profile.php" style="color: black; padding: 12px 16px; text-decoration: none; display: block;">Edit Profile</a>' . "\n                    " . '$2',
                    $content
                );
                
                if ($new_content !== $content) {
                    file_put_contents($file, $new_content);
                    echo "✓ Updated (alternative): $filename\n";
                    $updated++;
                } else {
                    echo "- Skipped (no match): $filename\n";
                    $skipped++;
                }
            } else {
                echo "- Skipped (already updated or no dropdown): $filename\n";
                $skipped++;
            }
        }
    } else {
        $skipped++;
    }
}

echo "\n=================================\n";
echo "Summary:\n";
echo "Updated: $updated files\n";
echo "Skipped: $skipped files\n";
echo "=================================\n";
?>
