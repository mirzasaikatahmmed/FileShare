# File Share Application - CasaOS Installation Guide

Complete guide to install and configure the Laravel File Sharing application on CasaOS with Nginx and MySQL.

## Prerequisites

Before starting, ensure you have:
- ✅ CasaOS installed and running
- ✅ Nginx installed in CasaOS
- ✅ MySQL installed in CasaOS
- ✅ PHP 8.2 or higher
- ✅ Composer (PHP package manager)
- ✅ SSH/Terminal access to your CasaOS system

## Required PHP Extensions

Make sure these PHP extensions are installed:

```bash
# Check installed PHP version
php -v

# Install required PHP extensions (Ubuntu/Debian)
sudo apt update
sudo apt install php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-sqlite3
```

## Step 1: Upload Application Files

### Option A: Using Git (Recommended)
```bash
# Navigate to web directory
cd /var/www/

# Clone the repository (or upload your files)
sudo git clone <your-repository-url> fileshare
# OR if you have files locally, upload via SFTP to /var/www/fileshare

# Set ownership
sudo chown -R www-data:www-data /var/www/fileshare
```

### Option B: Manual Upload
1. Upload all files from your local `FileShare` directory to `/var/www/fileshare` via SFTP
2. Ensure all files are uploaded including hidden files (`.env.example`)

## Step 2: Install Composer Dependencies

```bash
cd /var/www/fileshare

# Install Composer if not already installed
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install project dependencies
composer install --optimize-autoloader --no-dev
```

## Step 3: MySQL Database Setup

### Create Database and User

```bash
# Login to MySQL
sudo mysql -u root -p

# Or if using MySQL container in CasaOS, connect to it first
```

Run these SQL commands:

```sql
-- Create database
CREATE DATABASE fileshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with password (change 'your_secure_password')
CREATE USER 'fileshare_user'@'localhost' IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON fileshare.* TO 'fileshare_user'@'localhost';

-- If MySQL is in a container, use '%' instead of 'localhost'
-- CREATE USER 'fileshare_user'@'%' IDENTIFIED BY 'your_secure_password';
-- GRANT ALL PRIVILEGES ON fileshare.* TO 'fileshare_user'@'%';

-- Apply changes
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

## Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env file
nano .env
```

Update these values in `.env`:

```env
APP_NAME="File Share"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-casaos-ip-or-domain

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
# If MySQL is in container, use container IP or name
# DB_HOST=mysql-container-name
DB_PORT=3306
DB_DATABASE=fileshare
DB_USERNAME=fileshare_user
DB_PASSWORD=your_secure_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache Configuration
CACHE_STORE=database

# Queue Configuration
QUEUE_CONNECTION=database

# File Upload Configuration
UPLOAD_MAX_FILESIZE=100M
POST_MAX_SIZE=100M

# Hashids Configuration
HASHIDS_SALT=your-random-salt-string-here
HASHIDS_LENGTH=8
```

## Step 5: Set Proper Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/fileshare

# Set directory permissions
sudo find /var/www/fileshare -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/fileshare -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /var/www/fileshare/storage
sudo chmod -R 775 /var/www/fileshare/bootstrap/cache

# Create necessary directories
mkdir -p /var/www/fileshare/storage/app/private/files
mkdir -p /var/www/fileshare/storage/app/public/qrcodes
mkdir -p /var/www/fileshare/storage/framework/cache
mkdir -p /var/www/fileshare/storage/framework/sessions
mkdir -p /var/www/fileshare/storage/framework/views
mkdir -p /var/www/fileshare/storage/framework/tmp
mkdir -p /var/www/fileshare/storage/logs

# Set proper ownership again
sudo chown -R www-data:www-data /var/www/fileshare/storage
sudo chown -R www-data:www-data /var/www/fileshare/bootstrap/cache
```

## Step 6: Run Database Migrations

```bash
cd /var/www/fileshare

# Run migrations
php artisan migrate --force

# Seed default settings
php artisan db:seed --class=SettingsSeeder --force

# Create storage symlink
php artisan storage:link

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 7: Create Admin User

```bash
# Create admin user
php artisan tinker
```

In the tinker console:

```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('your-secure-password');
$user->email_verified_at = now();
$user->save();
exit
```

## Step 8: Configure Nginx

Create Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/fileshare
```

Add this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;

    # Change to your domain or IP
    server_name your-domain.com your-casaos-ip;

    root /var/www/fileshare/public;
    index index.php index.html;

    # Increase upload size limits
    client_max_body_size 100M;
    client_body_timeout 300s;

    # Logs
    access_log /var/log/nginx/fileshare-access.log;
    error_log /var/log/nginx/fileshare-error.log;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;

        # Increase timeout for large uploads
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /(\.env|\.git|storage|vendor|database|tests) {
        deny all;
        return 404;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site and reload Nginx:

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/fileshare /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# If test passes, reload Nginx
sudo systemctl reload nginx

# Enable Nginx to start on boot
sudo systemctl enable nginx
```

## Step 9: Configure PHP-FPM

Edit PHP-FPM configuration:

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Update these values:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
upload_tmp_dir = /var/www/fileshare/storage/framework/tmp
```

Edit PHP-FPM pool configuration:

```bash
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Ensure these settings:

```ini
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl enable php8.2-fpm
```

## Step 10: Set Up Automatic File Cleanup (Cron Job)

The application has a built-in command to automatically delete expired files.

```bash
# Edit crontab for www-data user
sudo crontab -e -u www-data

# Add this line to run cleanup daily at 2:00 AM
0 2 * * * cd /var/www/fileshare && php artisan schedule:run >> /dev/null 2>&1
```

Or use Laravel's scheduler (recommended):

```bash
# Edit crontab
sudo crontab -e -u www-data

# Add single entry for Laravel scheduler
* * * * * cd /var/www/fileshare && php artisan schedule:run >> /dev/null 2>&1
```

## Step 11: Optional - Set Up SSL with Let's Encrypt

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate (replace with your domain)
sudo certbot --nginx -d your-domain.com

# Auto-renewal is set up automatically
# Test renewal
sudo certbot renew --dry-run
```

## Step 12: Configure Firewall (Optional but Recommended)

```bash
# Allow HTTP and HTTPS
sudo ufw allow 'Nginx Full'

# Enable firewall if not already enabled
sudo ufw enable

# Check status
sudo ufw status
```

## Step 13: Final Verification

### Test Application

1. **Access Application:**
   ```
   http://your-casaos-ip
   or
   http://your-domain.com
   ```

2. **Test File Upload:**
   - Upload a test file
   - Verify it appears in admin dashboard

3. **Login to Admin:**
   ```
   http://your-casaos-ip/login
   Email: admin@example.com
   Password: your-secure-password
   ```

4. **Check Settings:**
   - Go to Admin → Settings
   - Configure allowed file types
   - Set upload limits

### Verify Services

```bash
# Check Nginx status
sudo systemctl status nginx

# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check MySQL status
sudo systemctl status mysql

# Check disk space
df -h

# Check Laravel logs
tail -f /var/www/fileshare/storage/logs/laravel.log
```

## Troubleshooting

### Permission Issues

```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/fileshare
sudo chmod -R 775 /var/www/fileshare/storage
sudo chmod -R 775 /var/www/fileshare/bootstrap/cache
```

### 500 Internal Server Error

```bash
# Check Laravel logs
tail -100 /var/www/fileshare/storage/logs/laravel.log

# Check Nginx error log
tail -100 /var/log/nginx/fileshare-error.log

# Clear cache
cd /var/www/fileshare
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Connection Failed

```bash
# Test MySQL connection
mysql -u fileshare_user -p fileshare

# Check MySQL status
sudo systemctl status mysql

# Verify .env database credentials
cat /var/www/fileshare/.env | grep DB_
```

### Upload Fails

```bash
# Check PHP upload limits
php -i | grep -i upload

# Check Nginx upload limit
sudo grep client_max_body_size /etc/nginx/sites-available/fileshare

# Verify storage permissions
ls -la /var/www/fileshare/storage/app/private/files/
```

### Session Not Persisting

```bash
# Check sessions table
mysql -u fileshare_user -p fileshare -e "SELECT COUNT(*) FROM sessions;"

# Clear sessions
cd /var/www/fileshare
php artisan tinker
>>> DB::table('sessions')->truncate();
>>> exit

# Verify session configuration
cat .env | grep SESSION
```

## Maintenance Commands

```bash
# Clear all caches
cd /var/www/fileshare
php artisan optimize:clear

# Run migrations (after updates)
php artisan migrate --force

# Clear old expired files manually
php artisan files:cleanup --days=7

# Check application status
php artisan about

# View queue jobs (if using queue)
php artisan queue:work --tries=3
```

## Security Best Practices

1. **Change default admin password immediately**
2. **Use strong database passwords**
3. **Enable HTTPS/SSL in production**
4. **Keep Laravel and dependencies updated:**
   ```bash
   composer update
   php artisan migrate
   ```
5. **Regular backups:**
   ```bash
   # Backup database
   mysqldump -u fileshare_user -p fileshare > backup_$(date +%Y%m%d).sql

   # Backup files
   tar -czf files_backup_$(date +%Y%m%d).tar.gz /var/www/fileshare/storage/app/private/files/
   ```
6. **Monitor logs regularly**
7. **Set up automatic security updates**

## Updating the Application

```bash
cd /var/www/fileshare

# Backup first!
mysqldump -u fileshare_user -p fileshare > backup_before_update.sql

# Pull updates (if using git)
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan optimize

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

## Support & Documentation

- Laravel Documentation: https://laravel.com/docs
- Application Admin Panel: http://your-domain.com/admin
- Settings Page: http://your-domain.com/admin/settings

## Default Credentials

**Admin Login:**
- Email: `admin@example.com`
- Password: `your-secure-password` (set during Step 7)

**Remember to change these immediately after first login!**

---

🎉 **Installation Complete!** Your file sharing application is now running on CasaOS with Nginx and MySQL.
