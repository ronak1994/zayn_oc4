#!/bin/bash

# Script to set up .twig files to open as PHP files by default on macOS

echo "Setting up .twig file association for PHP..."

# Create the file association
duti -s com.microsoft.VSCode .twig all
duti -s com.microsoft.VSCode .twig public.php

echo "File association set up complete!"
echo "Note: You may need to restart your editor for changes to take effect."
echo ""
echo "If you're using a different editor, you can manually set the association:"
echo "1. Right-click on any .twig file"
echo "2. Select 'Get Info'"
echo "3. Under 'Open with:', select your preferred editor"
echo "4. Click 'Change All...' to apply to all .twig files"
