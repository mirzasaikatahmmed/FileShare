#!/bin/bash

###############################################################################
# File Share Application - CasaOS Installation Script
# This script automates the installation process
###############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_header() {
    echo ""
    echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
    echo ""
}

# Check if running as root
check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_error "Please run this script with sudo"
        exit 1
    fi
}

# Get user input
get_config() {
    print_header "Configuration Setup"

    read -p "Enter your domain or IP address (e.g., fileshare.local or 192.168.1.100): " DOMAIN
    read -p "Enter MySQL root password: " -s MYSQL_ROOT_PASSWORD
    echo
    read -p "Enter new database password for fileshare_user: " -s DB_PASSWORD
    echo
    read -p "Enter admin email (default: admin@example.com): " ADMIN_EMAIL
    ADMIN_EMAIL=${ADMIN_EMAIL:-admin@example.com}
    read -p "Enter admin password: " -s ADMIN_PASSWORD
    echo

    print_success "Configuration collected"
}

# Install dependencies
install_dependencies() {
    print_header "Installing Dependencies"

    print_info "Updating package list..."
    apt update -qq

    print_info "Installing PHP and extensions..."
    apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
        php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-sqlite3 \
        unzip curl git > /dev/null 2>&1

    print_success "Dependencies installed"
}

# Install Composer
install_composer() {
    print_header "Installing Composer"

    if command -v composer &> /dev/null; then
        print_info "Composer already installed"
    else
        curl -sS https://getcomposer.org/installer | php
        mv composer.phar /usr/local/bin/composer
        chmod +x /usr/local/bin/composer
        print_success "Composer installed"
    fi
}

# Set up database
setup_database() {
    print_header "Setting Up Database"

    print_info "Creating database and user..."

    mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<EOF
CREATE DATABASE IF NOT EXISTS fileshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'fileshare_user'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON fileshare.* TO 'fileshare_user'@'localhost';
FLUSH PRIVILEGES;
EOF

    print_success "Database created successfully"
}

# Configure application
configure_app() {
    print_header "Configuring Application"

    print_info "Copying .env file..."
    cp .env.example .env

    print_info "Setting environment variables..."
    sed -i "s|APP_URL=.*|APP_URL=http://${DOMAIN}|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" .env
    sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env

    print_info "Generating application key..."
    php artisan key:generate --force

    print_info "Installing composer dependencies..."
    composer install --optimize-autoloader --no-dev --quiet

    print_success "Application configured"
}

# Set permissions
set_permissions() {
    print_header "Setting Permissions"

    print_info "Creating directories..."
    mkdir -p storage/app/private/files
    mkdir -p storage/app/public/qrcodes
    mkdir -p storage/framework/{cache,sessions,views,tmp}
    mkdir -p storage/logs
    mkdir -p bootstrap/cache

    print_info "Setting ownership..."
    chown -R www-data:www-data .

    print_info "Setting directory permissions..."
    find . -type d -exec chmod 755 {} \;
    find . -type f -exec chmod 644 {} \;

    chmod -R 775 storage
    chmod -R 775 bootstrap/cache

    print_success "Permissions set"
}

# Run migrations
run_migrations() {
    print_header "Running Database Migrations"

    print_info "Running migrations..."
    php artisan migrate --force

    print_info "Seeding default settings..."
    php artisan db:seed --class=SettingsSeeder --force

    print_info "Creating storage link..."
    php artisan storage:link

    print_success "Migrations completed"
}

# Create admin user
create_admin() {
    print_header "Creating Admin User"

    php artisan tinker --execute="
        \$user = new App\Models\User();
        \$user->name = 'Admin';
        \$user->email = '${ADMIN_EMAIL}';
        \$user->password = bcrypt('${ADMIN_PASSWORD}');
        \$user->email_verified_at = now();
        \$user->save();
        echo 'Admin user created successfully';
    "

    print_success "Admin user created"
}

# Configure Nginx
configure_nginx() {
    print_header "Configuring Nginx"

    print_info "Creating Nginx configuration..."

    cat > /etc/nginx/sites-available/fileshare <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root $(pwd)/public;
    index index.php index.html;

    client_max_body_size 100M;
    client_body_timeout 300s;

    access_log /var/log/nginx/fileshare-access.log;
    error_log /var/log/nginx/fileshare-error.log;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300s;
    }

    location ~ /\. {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}
EOF

    # Enable site
    ln -sf /etc/nginx/sites-available/fileshare /etc/nginx/sites-enabled/

    # Remove default site if exists
    rm -f /etc/nginx/sites-enabled/default

    print_info "Testing Nginx configuration..."
    nginx -t

    print_info "Reloading Nginx..."
    systemctl reload nginx
    systemctl enable nginx

    print_success "Nginx configured"
}

# Configure PHP-FPM
configure_php() {
    print_header "Configuring PHP-FPM"

    print_info "Updating PHP configuration..."

    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 100M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/post_max_size = .*/post_max_size = 100M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini
    sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini

    print_info "Restarting PHP-FPM..."
    systemctl restart php8.2-fpm
    systemctl enable php8.2-fpm

    print_success "PHP-FPM configured"
}

# Set up cron
setup_cron() {
    print_header "Setting Up Cron Job"

    print_info "Adding Laravel scheduler to cron..."

    (crontab -u www-data -l 2>/dev/null; echo "* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -

    print_success "Cron job configured"
}

# Optimize application
optimize_app() {
    print_header "Optimizing Application"

    print_info "Caching configuration..."
    php artisan config:cache

    print_info "Caching routes..."
    php artisan route:cache

    print_info "Caching views..."
    php artisan view:cache

    print_success "Application optimized"
}

# Print summary
print_summary() {
    print_header "Installation Complete!"

    echo ""
    echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║           Installation Successful! 🎉             ║${NC}"
    echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}Application URL:${NC} http://${DOMAIN}"
    echo -e "${BLUE}Admin Panel:${NC} http://${DOMAIN}/admin"
    echo ""
    echo -e "${BLUE}Admin Credentials:${NC}"
    echo -e "  Email: ${ADMIN_EMAIL}"
    echo -e "  Password: ${ADMIN_PASSWORD}"
    echo ""
    echo -e "${YELLOW}⚠ Important:${NC}"
    echo "  1. Change admin password after first login"
    echo "  2. Configure allowed file types in Admin → Settings"
    echo "  3. Set up SSL certificate for production use"
    echo "  4. Review and update .env file settings"
    echo ""
    echo -e "${GREEN}Next Steps:${NC}"
    echo "  • Visit http://${DOMAIN} to test file upload"
    echo "  • Login at http://${DOMAIN}/login"
    echo "  • Configure settings at http://${DOMAIN}/admin/settings"
    echo ""
    echo -e "${BLUE}Documentation:${NC} See CASAOS-INSTALLATION.md for details"
    echo ""
}

# Main installation flow
main() {
    clear
    echo ""
    echo -e "${BLUE}╔═══════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║     File Share Application Installer v1.0        ║${NC}"
    echo -e "${BLUE}║              For CasaOS + Nginx + MySQL           ║${NC}"
    echo -e "${BLUE}╚═══════════════════════════════════════════════════╝${NC}"
    echo ""

    check_root
    get_config

    install_dependencies
    install_composer
    setup_database
    configure_app
    set_permissions
    run_migrations
    create_admin
    configure_nginx
    configure_php
    setup_cron
    optimize_app

    print_summary
}

# Run installation
main
