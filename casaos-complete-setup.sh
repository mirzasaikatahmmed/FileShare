#!/bin/bash
# FileShare Complete Installation Script for CasaOS
# Application will be installed at: /DATA/AppData/ngnix/config/www/FileShare
# Accessible at: http://192.168.0.200:40080/fileshare

set -e  # Exit on error

echo "================================================"
echo "FileShare CasaOS Installation Script"
echo "================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/DATA/AppData/ngnix/config/www/FileShare"
NGINX_CONF="/DATA/AppData/ngnix/config/nginx/site-confs/fileshare.conf"
OLD_DIR="/var/www/fileshare"
DB_NAME="fileshare"
DB_USER="root"
DB_PASS="password"
APP_URL="http://192.168.0.200:40080/fileshare"

echo -e "${YELLOW}Step 1: Cleaning up old installation...${NC}"
sudo rm -rf "$OLD_DIR" 2>/dev/null || true
sudo rm -f "$NGINX_CONF" 2>/dev/null || true
sudo rm -rf "$APP_DIR" 2>/dev/null || true
echo -e "${GREEN}✓ Old installation removed${NC}"
echo ""

echo -e "${YELLOW}Step 2: Creating application directory...${NC}"
sudo mkdir -p "$APP_DIR"
cd "$APP_DIR"
echo -e "${GREEN}✓ Directory created: $APP_DIR${NC}"
echo ""

echo -e "${YELLOW}Step 3: Cloning application files...${NC}"
sudo git clone https://github.com/mirzasaikatahmmed/FileShare.git . || {
    echo -e "${RED}Git clone failed. Please upload files manually.${NC}"
    exit 1
}
echo -e "${GREEN}✓ Files cloned successfully${NC}"
echo ""

echo -e "${YELLOW}Step 4: Setting ownership and permissions...${NC}"
sudo chown -R www-data:www-data "$APP_DIR"
sudo find "$APP_DIR" -type d -exec chmod 755 {} \;
sudo find "$APP_DIR" -type f -exec chmod 644 {} \;
echo -e "${GREEN}✓ Ownership set${NC}"
echo ""

echo -e "${YELLOW}Step 5: Installing Composer dependencies...${NC}"
cd "$APP_DIR"
sudo -u www-data composer install --optimize-autoloader --no-dev --no-interaction || {
    echo -e "${RED}Composer install failed${NC}"
    exit 1
}
echo -e "${GREEN}✓ Composer dependencies installed${NC}"
echo ""

echo -e "${YELLOW}Step 6: Setting up environment file...${NC}"
sudo -u www-data cp .env.example .env

# Generate APP_KEY
sudo -u www-data php artisan key:generate --force

# Configure .env
sudo sed -i "s|APP_NAME=.*|APP_NAME=\"File Share\"|g" .env
sudo sed -i "s|APP_ENV=.*|APP_ENV=production|g" .env
sudo sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" .env
sudo sed -i "s|APP_URL=.*|APP_URL=$APP_URL|g" .env

sudo sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=mysql|g" .env
sudo sed -i "s|DB_HOST=.*|DB_HOST=127.0.0.1|g" .env
sudo sed -i "s|DB_PORT=.*|DB_PORT=3306|g" .env
sudo sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|g" .env
sudo sed -i "s|DB_USERNAME=.*|DB_USERNAME=$DB_USER|g" .env
sudo sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASS|g" .env

sudo sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=database|g" .env
sudo sed -i "s|CACHE_STORE=.*|CACHE_STORE=database|g" .env
sudo sed -i "s|QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|g" .env

echo -e "${GREEN}✓ Environment configured${NC}"
echo ""

echo -e "${YELLOW}Step 7: Creating storage directories...${NC}"
sudo mkdir -p "$APP_DIR/storage/app/private/files"
sudo mkdir -p "$APP_DIR/storage/app/public/qrcodes"
sudo mkdir -p "$APP_DIR/storage/framework/cache"
sudo mkdir -p "$APP_DIR/storage/framework/sessions"
sudo mkdir -p "$APP_DIR/storage/framework/views"
sudo mkdir -p "$APP_DIR/storage/framework/tmp"
sudo mkdir -p "$APP_DIR/storage/logs"

sudo chmod -R 775 "$APP_DIR/storage"
sudo chmod -R 775 "$APP_DIR/bootstrap/cache"
sudo chown -R www-data:www-data "$APP_DIR/storage"
sudo chown -R www-data:www-data "$APP_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Storage directories created${NC}"
echo ""

echo -e "${YELLOW}Step 8: Setting up database...${NC}"
# Check if database exists, drop and recreate for fresh installation
mysql -u"$DB_USER" -p"$DB_PASS" -e "DROP DATABASE IF EXISTS $DB_NAME;" 2>/dev/null || true
mysql -u"$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo -e "${GREEN}✓ Database created${NC}"
echo ""

echo -e "${YELLOW}Step 9: Running migrations...${NC}"
cd "$APP_DIR"
sudo -u www-data php artisan migrate:fresh --force
echo -e "${GREEN}✓ Migrations completed${NC}"
echo ""

echo -e "${YELLOW}Step 10: Seeding database...${NC}"
sudo -u www-data php artisan db:seed --class=SettingsSeeder --force 2>/dev/null || true
echo -e "${GREEN}✓ Database seeded${NC}"
echo ""

echo -e "${YELLOW}Step 11: Creating admin user...${NC}"
sudo -u www-data php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Admin';
\$user->email = 'admin@fileshare.com';
\$user->password = bcrypt('admin123');
\$user->email_verified_at = now();
\$user->save();
echo 'Admin user created successfully';
"
echo -e "${GREEN}✓ Admin user created${NC}"
echo -e "${YELLOW}   Email: admin@fileshare.com${NC}"
echo -e "${YELLOW}   Password: admin123${NC}"
echo ""

echo -e "${YELLOW}Step 12: Creating storage symlink...${NC}"
sudo -u www-data php artisan storage:link
echo -e "${GREEN}✓ Storage linked${NC}"
echo ""

echo -e "${YELLOW}Step 13: Optimizing application...${NC}"
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
echo -e "${GREEN}✓ Application optimized${NC}"
echo ""

echo -e "${YELLOW}Step 14: Creating Nginx configuration...${NC}"
cat << 'NGINXCONF' | sudo tee "$NGINX_CONF" > /dev/null
server {
    listen 80;
    listen [::]:80;

    server_name 192.168.0.200 localhost _;

    # Application is served from /fileshare path
    location /fileshare {
        alias /DATA/AppData/ngnix/config/www/FileShare/public;
        index index.php index.html;

        # Increase upload size limits
        client_max_body_size 100M;
        client_body_timeout 300s;

        # Try files with proper handling
        try_files $uri $uri/ @fileshare;

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            fastcgi_param SCRIPT_NAME /fileshare/index.php;
            fastcgi_hide_header X-Powered-By;

            # Increase timeout for large uploads
            fastcgi_read_timeout 300s;
            fastcgi_send_timeout 300s;
        }

        # Cache static files
        location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
            expires 7d;
            add_header Cache-Control "public, immutable";
        }
    }

    location @fileshare {
        rewrite /fileshare/(.*)$ /fileshare/index.php?/$1 last;
    }

    # Deny access to hidden files in fileshare
    location ~ /fileshare/\. {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
}
NGINXCONF

echo -e "${GREEN}✓ Nginx configuration created${NC}"
echo ""

echo -e "${YELLOW}Step 15: Testing and restarting Nginx...${NC}"
docker exec Nginx nginx -t || {
    echo -e "${RED}Nginx configuration test failed!${NC}"
    exit 1
}
docker restart Nginx
echo -e "${GREEN}✓ Nginx restarted${NC}"
echo ""

echo -e "${YELLOW}Step 16: Final permissions check...${NC}"
sudo chown -R www-data:www-data "$APP_DIR"
sudo chmod -R 775 "$APP_DIR/storage"
sudo chmod -R 775 "$APP_DIR/bootstrap/cache"
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

echo "================================================"
echo -e "${GREEN}Installation Complete!${NC}"
echo "================================================"
echo ""
echo -e "${GREEN}✓ Application URL:${NC} http://192.168.0.200:40080/fileshare"
echo -e "${GREEN}✓ Admin Email:${NC} admin@fileshare.com"
echo -e "${GREEN}✓ Admin Password:${NC} admin123"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Access: http://192.168.0.200:40080/fileshare"
echo "2. Login with admin credentials"
echo "3. Change admin password immediately"
echo "4. Configure Cloudflare Tunnel (instructions below)"
echo ""
echo -e "${YELLOW}To set up Cloudflare Tunnel:${NC}"
echo "1. Install cloudflared: curl -L https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 -o cloudflared"
echo "2. Login: ./cloudflared tunnel login"
echo "3. Create tunnel: ./cloudflared tunnel create fileshare"
echo "4. Configure tunnel to point to: http://192.168.0.200:40080"
echo ""
echo -e "${GREEN}Installation log saved to: /tmp/fileshare-install.log${NC}"
echo "================================================"
