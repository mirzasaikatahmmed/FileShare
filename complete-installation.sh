#!/bin/bash
# Complete FileShare Installation - Remaining Steps
# Run as: sudo bash complete-installation.sh

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

APP_DIR="/DATA/AppData/ngnix/config/www/FileShare"
NGINX_CONF="/DATA/AppData/ngnix/config/nginx/site-confs/fileshare.conf"
APP_URL="http://192.168.0.200:40080/fileshare"
ADMIN_EMAIL="admin@fileshare.com"
ADMIN_PASS="admin123"

echo "================================================"
echo -e "${BLUE}Completing FileShare Installation${NC}"
echo "================================================"
echo ""

cd "$APP_DIR"

echo -e "${YELLOW}[11/16] Running database migrations...${NC}"
sudo -u www-data php artisan migrate:fresh --force
echo -e "${GREEN}✓ Migrations completed${NC}"
echo ""

echo -e "${YELLOW}[12/16] Seeding database...${NC}"
sudo -u www-data php artisan db:seed --class=SettingsSeeder --force 2>/dev/null || echo "Seeder not found, skipping..."
echo -e "${GREEN}✓ Database seeded${NC}"
echo ""

echo -e "${YELLOW}[13/16] Creating admin user...${NC}"
sudo -u www-data php artisan tinker << 'TINKEREOF'
$user = App\Models\User::where('email', 'admin@fileshare.com')->first();
if ($user) { $user->delete(); }
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@fileshare.com';
$user->password = bcrypt('admin123');
$user->email_verified_at = now();
$user->save();
echo "Admin user created\n";
exit
TINKEREOF
echo -e "${GREEN}✓ Admin user created${NC}"
echo ""

echo -e "${YELLOW}[14/16] Creating storage symlink and optimizing...${NC}"
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
echo "  Email: admin@fileshare.com"
echo "  Password: admin123"
echo ""
echo -e "${YELLOW}Important:${NC} Change admin password after first login!"
echo ""
echo -e "${BLUE}Application Directory:${NC} $APP_DIR"
echo -e "${BLUE}Nginx Config:${NC} $NGINX_CONF"
echo ""
echo -e "${YELLOW}For Cloudflare Tunnel:${NC}"
echo "  Point your tunnel to: http://192.168.0.200:40080"
echo "  Your app will be accessible at: https://yourdomain.com/fileshare"
echo ""
echo -e "${YELLOW}Troubleshooting:${NC}"
echo "  View logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "  Nginx logs: docker logs Nginx"
echo "  Clear cache: cd $APP_DIR && sudo -u www-data php artisan cache:clear"
echo ""
echo "================================================"
