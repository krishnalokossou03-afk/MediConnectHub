#!/bin/bash
# Make sure this file has executable permissions, run `chmod +x build-app.sh`

# Exit the script if any command fails
set -e

# Clear cache


# Cache the various components of the Laravel application
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache