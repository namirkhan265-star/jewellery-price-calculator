#!/bin/bash
# CRITICAL FIX: Remove auto-initialization from diamonds class

echo "Fixing class-jpc-diamonds.php..."

# Remove the last two lines (the auto-initialization)
cd /path/to/wp-content/plugins/jewellery-price-calculator

# Create backup
cp includes/class-jpc-diamonds.php includes/class-jpc-diamonds.php.backup

# Remove last 2 lines
head -n -2 includes/class-jpc-diamonds.php > includes/class-jpc-diamonds.php.tmp
mv includes/class-jpc-diamonds.php.tmp includes/class-jpc-diamonds.php

echo "✓ Fixed! The auto-initialization has been removed."
echo "✓ Backup saved as: includes/class-jpc-diamonds.php.backup"
echo ""
echo "Now try activating the plugin again!"
