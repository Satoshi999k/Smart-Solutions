<?php
// Test image upload configuration

echo "=== Image Upload Test ===\n\n";

// Check PHP configurations
echo "1. PHP Upload Settings:\n";
echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size: " . ini_get('post_max_size') . "\n";
echo "   upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default system temp') . "\n\n";

// Check image directory
$image_dir = "../image/";
echo "2. Image Directory Check:\n";
echo "   Path: $image_dir\n";
echo "   Absolute path: " . realpath($image_dir) . "\n";
echo "   Exists: " . (is_dir($image_dir) ? "YES ✓" : "NO ✗") . "\n";
echo "   Readable: " . (is_readable($image_dir) ? "YES ✓" : "NO ✗") . "\n";
echo "   Writable: " . (is_writable($image_dir) ? "YES ✓" : "NO ✗") . "\n";

if (is_dir($image_dir)) {
    $perms = substr(sprintf('%o', fileperms($image_dir)), -4);
    echo "   Permissions: $perms\n";
}

echo "\n3. Sample file test:\n";
$test_file = $image_dir . "test_" . time() . ".txt";
if (@file_put_contents($test_file, "test")) {
    echo "   Write test: SUCCESS ✓\n";
    @unlink($test_file);
} else {
    echo "   Write test: FAILED ✗\n";
}

echo "\n4. Current working directory: " . getcwd() . "\n";
echo "\n✓ Check complete!\n";
?>
