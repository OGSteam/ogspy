#!/bin/bash
# Script to fix CRLF line endings in mod folder
# Converts all CRLF files to LF (Unix line endings)

MOD_DIR="/var/www/html/mod"

echo "Fixing CRLF line endings in mod folder..."
echo "==========================================="

# Find all files with CRLF and convert to LF
file_count=0
for file in $(find "$MOD_DIR" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.html" -o -name "*.md" -o -name "*.json" \) -exec grep -l $'\r' {} \;); do
    echo "Converting: $file"
    # Use dos2unix to convert line endings
    dos2unix "$file" 2>/dev/null || {
        # Fallback: use sed if dos2unix is not available
        sed -i 's/\r$//' "$file"
    }
    ((file_count++))
done

echo "==========================================="
echo "✓ Converted $file_count files from CRLF to LF"

# Verify no CRLF files remain
remaining=$(find "$MOD_DIR" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.html" -o -name "*.md" -o -name "*.json" \) -exec grep -l $'\r' {} \; | wc -l)

if [ "$remaining" -eq 0 ]; then
    echo "✓ All files have been converted successfully!"
else
    echo "⚠ Warning: $remaining files still have CRLF endings"
fi
