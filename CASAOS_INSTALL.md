# FileShare Installation Guide for CasaOS

This guide will help you install FileShare on CasaOS using Docker Compose.

## Prerequisites

- CasaOS installed and running
- Docker and Docker Compose installed on your system
- Basic knowledge of terminal/command line

## Quick Installation

### Step 1: Create Installation Directory

```bash
mkdir -p /DATA/AppData/fileshare
cd /DATA/AppData/fileshare
```

### Step 2: Download docker-compose.yml

Create a `docker-compose.yml` file with the following content:

```bash
curl -o docker-compose.yml https://raw.githubusercontent.com/yourusername/fileshare/main/docker-compose-casaos.yml
```

Or create it manually using the provided `docker-compose-casaos.yml` file.

### Step 3: Create Environment File

Create a `.env` file in the same directory:

```bash
nano .env
```

Add the following configuration (modify as needed):

```env
# Application Settings
APP_NAME=FileShare
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=http://your-server-ip

# Database Configuration
DB_DATABASE=fileshare
DB_USERNAME=postgres
DB_PASSWORD=your_secure_password_here

# Redis Configuration
REDIS_PASSWORD=null

# Application Port
APP_PORT=8080

# Database Port (if you need external access)
DB_PORT=5432

# Redis Port (if you need external access)
REDIS_PORT=6379

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Step 4: Generate Application Key

Before starting, you need to generate a Laravel application key. Run:

```bash
docker run --rm mirzasaikatahmmed/fileshare:latest php artisan key:generate --show
```

Copy the generated key and update the `APP_KEY` in your `.env` file.

### Step 5: Create Nginx Configuration

Create the nginx configuration directory and file:

```bash
mkdir -p docker/nginx
nano docker/nginx/nginx.conf
```

Add the following Nginx configuration:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires max;
        add_header Cache-Control "public, immutable";
    }
}
```

### Step 6: Start the Application

```bash
docker-compose up -d
```

### Step 7: Run Database Migrations

After the containers are running, execute migrations:

```bash
docker exec -it fileshare_app php artisan migrate --force
```

### Step 8: Set Proper Permissions

```bash
docker exec -it fileshare_app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec -it fileshare_app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
```

### Step 9: Access Your Application

Open your web browser and navigate to:
- `http://your-server-ip:8080` (or the port you configured in APP_PORT)

## Post-Installation

### Create Admin User

If your application supports admin user creation via command:

```bash
docker exec -it fileshare_app php artisan user:create
```

### Clear Cache

```bash
docker exec -it fileshare_app php artisan cache:clear
docker exec -it fileshare_app php artisan config:clear
docker exec -it fileshare_app php artisan view:clear
```

### Optimize Application

```bash
docker exec -it fileshare_app php artisan config:cache
docker exec -it fileshare_app php artisan route:cache
docker exec -it fileshare_app php artisan view:cache
```

## Managing the Application

### View Logs

```bash
# Application logs
docker-compose logs -f app

# Nginx logs
docker-compose logs -f nginx

# Queue worker logs
docker-compose logs -f queue

# All logs
docker-compose logs -f
```

### Restart Services

```bash
# Restart all services
docker-compose restart

# Restart specific service
docker-compose restart app
```

### Stop Services

```bash
docker-compose stop
```

### Remove Everything (Careful!)

```bash
# This will remove containers and volumes (all data will be lost)
docker-compose down -v
```

## Updating the Application

### Pull Latest Images

```bash
docker-compose pull
```

### Restart with New Images

```bash
docker-compose up -d
```

### Run Migrations After Update

```bash
docker exec -it fileshare_app php artisan migrate --force
```

## Troubleshooting

### Check Container Status

```bash
docker-compose ps
```

### Check Container Logs

```bash
docker-compose logs app
```

### Enter Container Shell

```bash
docker exec -it fileshare_app sh
```

### Permission Issues

If you encounter permission issues:

```bash
docker exec -it fileshare_app chown -R www-data:www-data /var/www/html
docker exec -it fileshare_app chmod -R 775 /var/www/html/storage
```

### Database Connection Issues

1. Check if PostgreSQL is running:
   ```bash
   docker-compose ps postgres
   ```

2. Check PostgreSQL logs:
   ```bash
   docker-compose logs postgres
   ```

3. Verify credentials in `.env` file match the docker-compose.yml

## Backup

### Backup Database

```bash
docker exec fileshare_postgres pg_dump -U postgres fileshare > backup_$(date +%Y%m%d).sql
```

### Backup Uploaded Files

```bash
docker cp fileshare_app:/var/www/html/storage ./storage_backup
```

## Restore

### Restore Database

```bash
docker exec -i fileshare_postgres psql -U postgres fileshare < backup_20240101.sql
```

### Restore Uploaded Files

```bash
docker cp ./storage_backup fileshare_app:/var/www/html/storage
docker exec -it fileshare_app chown -R www-data:www-data /var/www/html/storage
```

## Support

For issues and support:
- GitHub Issues: [Your Repository URL]
- Documentation: [Your Docs URL]

## Docker Hub Images

- Application: `mirzasaikatahmmed/fileshare:latest`
- Queue Worker: `mirzasaikatahmmed/fileshare-queue:latest`

## Security Notes

1. **Change default passwords**: Update `DB_PASSWORD` in `.env`
2. **Generate APP_KEY**: Always generate a unique APP_KEY
3. **Use HTTPS**: Configure reverse proxy (Nginx Proxy Manager, Traefik, etc.)
4. **Firewall**: Only expose necessary ports
5. **Regular updates**: Keep Docker images updated

## Performance Tips

1. **Resource Allocation**: Allocate appropriate CPU and memory to containers
2. **Redis**: Use Redis for cache and sessions in production
3. **Queue Workers**: Monitor queue workers for optimal performance
4. **Database**: Regular database optimization and vacuum

---

**Note**: This installation is optimized for CasaOS but will work on any Docker-compatible system.
