@echo off
echo Updating all PHP files to add Edit Profile to dropdown...
echo.

php -r "$files = glob('*.php'); foreach($files as $file) { if (in_array($file, ['UPDATE_DROPDOWN.php', 'conn.php', 'logout.php', 'edit-profile.php', 'update-profile.php', 'update_profile.php'])) continue; $content = file_get_contents($file); if (strpos($content, 'dropdown-menu') !== false && strpos($content, 'edit-profile.php') === false && strpos($content, 'logout.php') !== false) { $content = preg_replace('/(<\?php if \(isset\(\$_SESSION\[[\'\']user_id[\'\']\]\)\): \?>[\s\S]*?)(<a href=[\"'']logout\.php)/i', '$1<a href=\"edit-profile.php\" style=\"color: black; padding: 12px 16px; text-decoration: none; display: block;\">Edit Profile</a>\n                $2', $content); file_put_contents($file, $content); echo \"Updated: $file\n\"; } }"

echo.
echo Done!
pause
