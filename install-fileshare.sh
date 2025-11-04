#!/bin/bash
# FileShare Complete Installation Script for CasaOS
# Run this script as: sudo bash install-fileshare.sh

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
APP_DIR="/DATA/AppData/ngnix/config/www/FileShare"
NGINX_CONF="/DATA/AppData/ngnix/config/nginx/site-confs/fileshare.conf"
OLD_DIR="/var/www/fileshare"
DB_NAME="fileshare"
DB_USER="root"
DB_PASS="password"
APP_URL="http://192.168.0.200:40080/fileshare"
ADMIN_EMAIL="admin@fileshare.com"
ADMIN_PASS="admin123"

clear
echo "================================================"
echo -e "${BLUE}FileShare CasaOS Complete Installation${NC}"
echo "================================================"
echo ""
echo "This will install FileShare at:"
echo "  Directory: $APP_DIR"
echo "  URL: $APP_URL"
echo ""
read -p "Press Enter to continue or Ctrl+C to cancel..."
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root: sudo bash install-fileshare.sh${NC}"
    exit 1
fi

echo -e "${YELLOW}[1/16] Cleaning up old installation...${NC}"
rm -rf "$OLD_DIR" 2>/dev/null || true
rm -f "$NGINX_CONF" 2>/dev/null || true
rm -rf "$APP_DIR" 2>/dev/null || true
echo -e "${GREEN}✓ Old files removed${NC}"
echo ""

echo -e "${YELLOW}[2/16] Creating application directory...${NC}"
mkdir -p "$APP_DIR"
echo -e "${GREEN}✓ Directory created: $APP_DIR${NC}"
echo ""

echo -e "${YELLOW}[3/16] Checking for local files...${NC}"
if [ -d "/tmp/FileShare" ]; then
    echo "Copying from /tmp/FileShare..."
    cp -r /tmp/FileShare/* "$APP_DIR/"
    cp -r /tmp/FileShare/.[!.]* "$APP_DIR/" 2>/dev/null || true
    echo -e "${GREEN}✓ Files copied${NC}"
elif [ -f "$APP_DIR/../../../FileShare.zip" ]; then
    echo "Extracting from zip..."
    unzip -q "$APP_DIR/../../../FileShare.zip" -d "$APP_DIR"
    echo -e "${GREEN}✓ Files extracted${NC}"
else
    echo "Cloning from GitHub..."
    cd "$APP_DIR"
    git clone https://github.com/mirzasaikatahmmed/FileShare.git .
    echo -e "${GREEN}✓ Files cloned${NC}"
fi
echo ""

echo -e "${YELLOW}[4/16] Setting file ownership...${NC}"
chown -R www-data:www-data "$APP_DIR"
echo -e "${GREEN}✓ Ownership set to www-data${NC}"
echo ""

echo -e "${YELLOW}[5/16] Setting file permissions...${NC}"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;
chmod -R 775 "$APP_DIR/storage" 2>/dev/null || true
chmod -R 775 "$APP_DIR/bootstrap/cache" 2>/dev/null || true
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

echo -e "${YELLOW}[6/16] Checking Composer...${NC}"
if ! command -v composer &> /dev/null; then
    echo "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    echo -e "${GREEN}✓ Composer installed${NC}"
else
    echo -e "${GREEN}✓ Composer already installed${NC}"
fi
echo ""

echo -e "${YELLOW}[7/16] Installing PHP dependencies...${NC}"
cd "$APP_DIR"
sudo -u www-data composer install --optimize-autoloader --no-dev --no-interaction
echo -e "${GREEN}✓ Dependencies installed${NC}"
echo ""

echo -e "${YELLOW}[8/16] Creating storage directories...${NC}"
mkdir -p "$APP_DIR/storage/app/private/files"
mkdir -p "$APP_DIR/storage/app/public/qrcodes"
mkdir -p "$APP_DIR/storage/framework/cache"
mkdir -p "$APP_DIR/storage/framework/sessions"
mkdir -p "$APP_DIR/storage/framework/views"
mkdir -p "$APP_DIR/storage/framework/tmp"
mkdir -p "$APP_DIR/storage/logs"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"
chown -R www-data:www-data "$APP_DIR/storage"
chown -R www-data:www-data "$APP_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Storage directories created${NC}"
echo ""

echo -e "${YELLOW}[9/16] Configuring environment...${NC}"
if [ ! -f "$APP_DIR/.env" ]; then
    cp "$APP_DIR/.env.example" "$APP_DIR/.env"
    chown www-data:www-data "$APP_DIR/.env"
fi

# Generate application key
sudo -u www-data php artisan key:generate --force

# Update .env file
sed -i "s|APP_NAME=.*|APP_NAME=\"File Share\"|" "$APP_DIR/.env"
sed -i "s|APP_ENV=.*|APP_ENV=production|" "$APP_DIR/.env"
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" "$APP_DIR/.env"
sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" "$APP_DIR/.env"

sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|" "$APP_DIR/.env"
sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|" "$APP_DIR/.env"
sed -i "s|DB_PORT=.*|DB_PORT=3306|" "$APP_DIR/.env"
sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" "$APP_DIR/.env"
sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|" "$APP_DIR/.env"
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|" "$APP_DIR/.env"

sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=database|" "$APP_DIR/.env"
sed -i "s|CACHE_STORE=.*|CACHE_STORE=database|" "$APP_DIR/.env"
sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|" "$APP_DIR/.env"

echo -e "${GREEN}✓ Environment configured${NC}"
echo ""

echo -e "${YELLOW}[10/16] Setting up database...${NC}"
mysql -u"$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS $DB_NAME;" 2>/dev/null || true
mysql -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo -e "${GREEN}✓ Database created${NC}"
echo ""

echo -e "${YELLOW}[11/16] Running database migrations...${NC}"
cd "$APP_DIR"
sudo -u www-data php artisan migrate:fresh --force
echo -e "${GREEN}✓ Migrations completed${NC}"
echo ""

echo -e "${YELLOW}[12/16] Seeding database...${NC}"
sudo -u www-data php artisan db:seed --class=SettingsSeeder --force 2>/dev/null || echo "Seeder not found, skipping..."
echo -e "${GREEN}✓ Database seeded${NC}"
echo ""

echo -e "${YELLOW}[13/16] Creating admin user...${NC}"
sudo -u www-data php artisan tinker << EOF
\$user = App\Models\User::where('email', '$ADMIN_EMAIL')->first();
if (\$user) { \$user->delete(); }
\$user = new App\Models\User();
\$user->name = 'Admin';
\$user->email = '$ADMIN_EMAIL';
\$user->password = bcrypt('$ADMIN_PASS');
\$user->email_verified_at = now();
\$user->save();
echo "Admin user created\n";
exit
EOF
echo -e "${GREEN}✓ Admin user created${NC}"
echo ""

echo -e "${YELLOW}[14/16] Creating storage symlink and optimizing...${NC}"
cd "$APP_DIR"
sudo -u www-data php artisan storage:link
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
echo -e "${GREEN}✓ Application optimized${NC}"
echo ""

echo -e "${YELLOW}[15/16] Configuring Nginx...${NC}"
cat > "$NGINX_CONF" << 'NGINXEOF'
server {
    listen 80;
    listen [::]:80;

    server_name 192.168.0.200 localhost _;

    # Logs
    access_log /config/log/nginx/fileshare-access.log;
    error_log /config/log/nginx/fileshare-error.log;

    # Main location block for /fileshare
    location /fileshare {
        alias /DATA/AppData/ngnix/config/www/FileShare/public;
        index index.php index.html;

        # Increase upload size limits
        client_max_body_size 100M;
        client_body_timeout 300s;

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;

        # Try to serve file directly, fallback to index.php
        try_files $uri $uri/ @fileshare_rewrite;

        # Handle PHP files
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            fastcgi_param PATH_INFO $fastcgi_path_info;
            fastcgi_hide_header X-Powered-By;

            # Increase timeouts for large uploads
            fastcgi_read_timeout 300s;
            fastcgi_send_timeout 300s;
        }

        # Static files caching
        location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
            expires 7d;
            add_header Cache-Control "public, immutable";
            access_log off;
        }
    }

    # Rewrite handler for Laravel routing
    location @fileshare_rewrite {
        rewrite ^/fileshare/(.*)$ /fileshare/index.php?/$1 last;
    }

    # Deny access to hidden files
    location ~ /fileshare/\..* {
        deny all;
        return 404;
    }

    # Deny access to sensitive directories
    location ~ /fileshare/(\.env|\.git|storage|vendor|database|tests|bootstrap) {
        deny all;
        return 404;
    }
}
NGINXEOF

chown saikat:saikat "$NGINX_CONF"
chmod 644 "$NGINX_CONF"
echo -e "${GREEN}✓ Nginx configuration created${NC}"
echo ""

echo -e "${YELLOW}[16/16] Restarting Nginx...${NC}"
if docker ps | grep -q Nginx; then
    docker exec Nginx nginx -t
    if [ $? -eq 0 ]; then
        docker restart Nginx
        echo -e "${GREEN}✓ Nginx restarted successfully${NC}"
    else
        echo -e "${RED}✗ Nginx configuration test failed${NC}"
        exit 1
    fi
else
    echo -e "${RED}✗ Nginx container not found${NC}"
    exit 1
fi
echo ""

echo -e "${YELLOW}Final permissions check...${NC}"
chown -R www-data:www-data "$APP_DIR"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"
echo -e "${GREEN}✓ All permissions set${NC}"
echo ""

echo "================================================"
echo -e "${GREEN}Installation Complete! 🎉${NC}"
echo "================================================"
echo ""
echo -e "${BLUE}Access your application:${NC}"
echo "  Local: http://192.168.0.200:40080/fileshare"
echo ""
echo -e "${BLUE}Admin Credentials:${NC}"
echo "  Email: $ADMIN_EMAIL"
echo "  Password: $ADMIN_PASS"
echo ""
echo -e "${YELLOW}Important:${NC} Change admin password after first login!"
echo ""
echo -e "${BLUE}Application Directory:${NC} $APP_DIR"
echo -e "${BLUE}Nginx Config:${NC} $NGINX_CONF"
echo -e "${BLUE}Database:${NC} $DB_NAME"
echo ""
echo -e "${YELLOW}Troubleshooting:${NC}"
echo "  View logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "  Nginx logs: docker logs Nginx"
echo "  Clear cache: cd $APP_DIR && php artisan cache:clear"
echo ""
echo "================================================"
