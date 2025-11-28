#!/usr/bin/env python3
import os
import re

files_to_fix = {
    'products/graphicscard.php': {
        'category': 'graphicscard',
        'start_id': 35,
        'end_id': 46
    },
    'products/pccase.php': {
        'category': 'pccase',
        'start_id': 83,
        'end_id': 94
    },
    'products/powersupply.php': {
        'category': 'powersupply',
        'start_id': 71,
        'end_id': 82
    }
}

base_path = 'd:/xampp/htdocs/ITP122/'

for file_path, info in files_to_fix.items():
    full_path = base_path + file_path
    print(f"Processing: {file_path}")
    
    with open(full_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Pattern to match the hardcoded $products = [ ... ]; section
    # This matches from $products = [ to ]; followed by foreach
    pattern = r'\s*\$products = \[[\s\S]*?\];[\s\n]*foreach \(\$products as \$product\) \{'
    
    # Replacement that includes the if check
    replacement = '''if (!empty($products)) {
        foreach ($products as $product) {'''
    
    new_content = re.sub(pattern, replacement, content)
    
    # Also need to fix the closing of the loop to include } else { and }
    # Find the closing of the foreach
    closing_pattern = r'(\s*\})(\s*\?>\s*</div>)'
    closing_replacement = r'''}
    } else {
        echo "<p style='grid-column: 1/-1; text-align: center;'>No ''' + info['category'] + ''' products available at the moment.</p>";
    }\2'''
    
    new_content = re.sub(closing_pattern, closing_replacement, new_content)
    
    with open(full_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"  ✓ Fixed {file_path}")

print("\nAll files processed!")
