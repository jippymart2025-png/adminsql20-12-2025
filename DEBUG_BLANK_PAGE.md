# Debugging Blank Page Issue on Hostinger VPS

## Common Causes and Solutions

### 1. Check Laravel Logs
```bash
# SSH into your VPS and check the logs
tail -f storage/logs/laravel.log
# Or check today's log
cat storage/logs/laravel-$(date +%Y-%m-%d).log
```

### 2. Enable Error Display (Temporarily)
Add this to the top of `resources/views/orders/edit.blade.php` (remove after debugging):
```php
@php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
@endphp
```

### 3. Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### 4. Check File Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 5. Check for PHP Syntax Errors
```bash
php -l resources/views/orders/edit.blade.php
```

### 6. Check File Encoding (BOM Issue)
The file should be UTF-8 without BOM. If you see invisible characters:
```bash
# Remove BOM if present
sed -i '1s/^\xEF\xBB\xBF//' resources/views/orders/edit.blade.php
```

### 7. Check for Missing Dependencies
Make sure all required JavaScript libraries are loaded in `layouts/app.blade.php`

### 8. Check Browser Console
Open browser DevTools (F12) and check:
- Console tab for JavaScript errors
- Network tab for failed resource loads

### 9. Verify Route Exists
```bash
php artisan route:list | grep orders
```

### 10. Check PHP Error Log
```bash
# Check PHP error log
tail -f /var/log/php-fpm/error.log
# Or
tail -f /var/log/apache2/error.log
```

## Quick Fix Commands (Run on VPS)
```bash
cd /path/to/your/project
php artisan optimize:clear
chmod -R 755 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## If Still Blank
1. Check if other pages work (to isolate the issue)
2. Try accessing the route directly: `/orders/{id}/edit`
3. Check if the controller method exists and returns the view
4. Verify the `layouts/app.blade.php` file exists

