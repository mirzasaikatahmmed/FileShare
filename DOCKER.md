# FileShare Docker Setup

This document provides instructions for running the FileShare application using Docker and Docker Compose.

## Prerequisites

- Docker Engine 20.10 or higher
- Docker Compose 2.0 or higher
- At least 2GB of free disk space

## Quick Start

### 1. Clone or Navigate to Project Directory

```bash
cd /path/to/FileShare
```

### 2. Copy Environment File

Copy the Docker environment file:

```bash
cp .env.docker .env
```

Or manually create a `.env` file with the following configuration:

```env
APP_NAME="File Share"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost
APP_PORT=80

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=fileshare
DB_USERNAME=postgres
DB_PASSWORD=root
```

### 3. Generate Application Key

If you don't have an `APP_KEY` in your `.env` file, generate one:

```bash
docker-compose run --rm app php artisan key:generate
```

### 4. Build and Start Containers

Build the Docker images and start all services:

```bash
docker-compose up -d --build
```

This command will:
- Build the Laravel application image
- Start PostgreSQL database
- Start Nginx web server
- Start Redis cache server
- Start Queue worker
- Run database migrations
- Create admin user

### 5. Access the Application

Open your browser and navigate to:

```
http://localhost
```

**Default Admin Credentials:**
- Email: `admin@fileshare.com`
- Password: `password`

⚠️ **Important:** Change the default password after first login!

## Docker Services

The application runs with the following services:

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| app | fileshare_app | 9000 | PHP-FPM Laravel application |
| nginx | fileshare_nginx | 80 | Nginx web server |
| postgres | fileshare_postgres | 5432 | PostgreSQL 17 database |
| redis | fileshare_redis | 6379 | Redis cache server |
| queue | fileshare_queue | - | Laravel queue worker |

## Common Commands

### View Container Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f postgres
```

### Stop Containers

```bash
docker-compose stop
```

### Start Stopped Containers

```bash
docker-compose start
```

### Restart Containers

```bash
docker-compose restart
```

### Stop and Remove Containers

```bash
docker-compose down
```

### Remove Containers and Volumes (⚠️ This will delete all data)

```bash
docker-compose down -v
```

### Execute Commands Inside Containers

```bash
# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Access bash shell
docker-compose exec app bash

# Access PostgreSQL shell
docker-compose exec postgres psql -U postgres -d fileshare
```

### Rebuild Containers

```bash
docker-compose up -d --build --force-recreate
```

## Database Management

### Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### Rollback Migrations

```bash
docker-compose exec app php artisan migrate:rollback
```

### Seed Database

```bash
docker-compose exec app php artisan db:seed
```

### Create Admin User

```bash
docker-compose exec app php artisan db:seed --class=AdminSeeder
```

### Database Backup

```bash
docker-compose exec postgres pg_dump -U postgres fileshare > backup.sql
```

### Database Restore

```bash
cat backup.sql | docker-compose exec -T postgres psql -U postgres fileshare
```

## Storage and File Uploads

The application stores uploaded files in the `storage` directory. This directory is mounted as a volume to persist data across container restarts.

### Set Storage Permissions

```bash
docker-compose exec app chown -R www-data:www-data storage
docker-compose exec app chmod -R 775 storage
```

### Clear Storage Caches

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan config:clear
```

## Queue Management

The queue worker runs automatically in a separate container.

### View Queue Logs

```bash
docker-compose logs -f queue
```

### Restart Queue Worker

```bash
docker-compose restart queue
```

### Process Jobs Manually

```bash
docker-compose exec app php artisan queue:work --once
```

## Performance Optimization

### Enable Production Caching

```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Clear All Caches

```bash
docker-compose exec app php artisan optimize:clear
```

## Troubleshooting

### Container Won't Start

1. Check logs:
   ```bash
   docker-compose logs app
   ```

2. Verify `.env` file exists and is properly configured

3. Ensure ports 80, 5432, and 6379 are not already in use

### Database Connection Issues

1. Check if PostgreSQL is running:
   ```bash
   docker-compose ps postgres
   ```

2. Verify database credentials in `.env` file

3. Wait for database to be ready:
   ```bash
   docker-compose logs postgres
   ```

### Permission Issues

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Application Shows 500 Error

1. Enable debug mode temporarily:
   ```bash
   # In .env file
   APP_DEBUG=true
   ```

2. Clear caches:
   ```bash
   docker-compose exec app php artisan optimize:clear
   ```

3. Check application logs:
   ```bash
   docker-compose logs -f app
   docker-compose exec app tail -f storage/logs/laravel.log
   ```

## Production Deployment

### Security Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Change `DB_PASSWORD` to a strong password
- [ ] Change default admin password
- [ ] Set `SESSION_SECURE_COOKIE=true` if using HTTPS
- [ ] Configure proper `APP_URL`
- [ ] Enable Redis for cache and sessions (optional)
- [ ] Set up proper backup strategy
- [ ] Configure email settings for notifications
- [ ] Review and adjust file upload limits

### Using Custom Ports

Edit the `.env` file:

```env
APP_PORT=8080  # Custom HTTP port
DB_PORT=5433   # Custom PostgreSQL port
REDIS_PORT=6380 # Custom Redis port
```

Then restart:

```bash
docker-compose down
docker-compose up -d
```

### SSL/TLS Configuration

For production with SSL, you'll need to:

1. Add SSL certificates to `docker/nginx/certs/`
2. Update `docker/nginx/nginx.conf` with SSL configuration
3. Update `APP_URL` to use `https://`
4. Set `SESSION_SECURE_COOKIE=true`

## Docker Image Management

### View Images

```bash
docker images | grep fileshare
```

### Remove Old Images

```bash
docker image prune -a
```

### Export Image

```bash
docker save fileshare_app:latest | gzip > fileshare_app.tar.gz
```

### Import Image

```bash
docker load < fileshare_app.tar.gz
```

## Health Checks

The PostgreSQL and Redis containers include health checks. View status:

```bash
docker-compose ps
```

Healthy services will show `(healthy)` status.

## Resource Limits

To set resource limits, edit `docker-compose.yml`:

```yaml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2'
          memory: 2G
```

## Support

For issues specific to Docker setup, check:
- Docker logs: `docker-compose logs`
- Container status: `docker-compose ps`
- Laravel logs: `storage/logs/laravel.log`

For application-related issues, refer to the main README.md file.
