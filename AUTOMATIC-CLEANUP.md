# Automatic File Cleanup

This application includes an automatic file cleanup system that deletes files after 7 days from their upload time.

## How It Works

Files are automatically deleted **7 days (168 hours)** after they are uploaded. The cleanup process:

1. Runs daily at **2:00 AM**
2. Finds all files where `created_at` is older than 7 days
3. Deletes the physical file from storage
4. Deletes the associated QR code
5. Logs the deletion in activity logs
6. Soft deletes the database record

## Manual Cleanup Commands

### Test Cleanup (Dry Run)
See what files would be deleted without actually deleting them:

```bash
php artisan files:cleanup --dry-run
```

### Run Cleanup Manually
Delete expired files immediately:

```bash
php artisan files:cleanup
```

### Custom Cleanup Period
Delete files older than a specific number of days:

```bash
# Delete files older than 14 days
php artisan files:cleanup --days=14

# Delete files older than 30 days
php artisan files:cleanup --days=30
```

## Scheduled Task

The cleanup command is scheduled to run automatically every day at 2:00 AM:

```php
$schedule->command('files:cleanup --days=7')
    ->dailyAt('02:00')
    ->withoutOverlapping();
```

### View Scheduled Tasks

```bash
php artisan schedule:list
```

### Test Scheduled Tasks

```bash
php artisan schedule:run
```

## Production Setup

To enable automatic cleanup in production, you need to set up a cron job that runs Laravel's scheduler.

### Linux/Ubuntu Server

1. Open crontab editor:
```bash
crontab -e
```

2. Add this line (replace `/path/to/your/project` with your actual project path):
```cron
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

Example:
```cron
* * * * * cd /var/www/html/FileShare && php artisan schedule:run >> /dev/null 2>&1
```

3. Save and exit (Ctrl+X, then Y, then Enter)

4. Verify cron is running:
```bash
crontab -l
```

### Windows Server (Task Scheduler)

1. Open **Task Scheduler**

2. Create a new **Basic Task**:
   - Name: `Laravel Scheduler`
   - Description: `Run Laravel scheduled tasks`

3. Trigger: **Daily**
   - Start: Today
   - Recur every: 1 day
   - Repeat task every: **1 minute**
   - For a duration of: **Indefinitely**

4. Action: **Start a program**
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `artisan schedule:run`
   - Start in: `D:\LocalHost\xampp\htdocs\FileShare`

5. Finish and enable the task

### Docker

Add to your docker-compose.yml:

```yaml
services:
  scheduler:
    image: your-app-image
    command: php artisan schedule:work
    volumes:
      - .:/var/www/html
    depends_on:
      - app
      - db
```

Or use a separate cron container:

```yaml
services:
  cron:
    image: your-app-image
    command: >
      sh -c "while true; do
        php artisan schedule:run --verbose --no-interaction &
        sleep 60
      done"
```

## Monitoring

### Check Logs

View cleanup activity in Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

### Activity Logs

All automatic deletions are logged in the `activity_logs` table with action `auto_delete`.

You can view them in the admin panel under **Activity Logs** or query directly:

```sql
SELECT * FROM activity_logs WHERE action = 'auto_delete' ORDER BY created_at DESC;
```

### Cleanup Statistics

The cleanup command provides detailed output:

```
==============================================
Cleanup Summary:
==============================================
Files processed: 25
Successfully deleted: 25 files
Failed: 0 files
Total space freed: 152.43 MB
==============================================
```

## Troubleshooting

### Scheduler Not Running

1. **Check cron is active:**
```bash
sudo systemctl status cron  # Ubuntu/Debian
sudo systemctl status crond  # CentOS/RHEL
```

2. **Test scheduler manually:**
```bash
php artisan schedule:run
```

3. **Check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

### Files Not Being Deleted

1. **Check if files exist:**
```bash
php artisan files:cleanup --dry-run
```

2. **Verify schedule is registered:**
```bash
php artisan schedule:list
```

3. **Check file permissions:**
```bash
# Ensure storage directories are writable
chmod -R 775 storage/
chown -R www-data:www-data storage/  # Ubuntu/Debian
```

### Disable Automatic Cleanup

To disable automatic cleanup, comment out the schedule in `bootstrap/app.php`:

```php
->withSchedule(function (Schedule $schedule): void {
    // Commented out automatic cleanup
    // $schedule->command('files:cleanup --days=7')
    //     ->dailyAt('02:00')
    //     ->withoutOverlapping();
})
```

## Customization

### Change Cleanup Time

Edit `bootstrap/app.php`:

```php
// Run at different time
$schedule->command('files:cleanup --days=7')->dailyAt('03:30');

// Run hourly
$schedule->command('files:cleanup --days=7')->hourly();

// Run weekly (Sundays at 2 AM)
$schedule->command('files:cleanup --days=7')->weekly()->sundays()->at('02:00');
```

### Change Default Days

Edit `bootstrap/app.php`:

```php
// Delete files after 14 days instead of 7
$schedule->command('files:cleanup --days=14')->dailyAt('02:00');
```

### Add Email Notifications

Edit `bootstrap/app.php`:

```php
$schedule->command('files:cleanup --days=7')
    ->dailyAt('02:00')
    ->emailOutputTo('admin@example.com');
```

### Add Slack Notifications

```php
$schedule->command('files:cleanup --days=7')
    ->dailyAt('02:00')
    ->thenPing('https://hooks.slack.com/services/...');
```

## Configuration

The cleanup behavior is defined in the command itself:

- **Location:** `app/Console/Commands/CleanupExpiredFiles.php`
- **Default days:** 7 days
- **Configurable via:** `--days` option

You can modify the command to:
- Change deletion logic
- Add additional cleanup rules
- Modify logging behavior
- Send notifications

## Security Notes

1. **Soft Deletes:** Files are soft-deleted by default, allowing recovery if needed
2. **Activity Logging:** All deletions are logged for audit purposes
3. **Dry Run:** Always test with `--dry-run` before running in production
4. **Backups:** Consider backing up files before automatic deletion

## Performance

For large file volumes:

1. **Add Database Index:** The cleanup queries use `created_at` column - ensure it's indexed
2. **Batch Processing:** The command processes files one by one to avoid memory issues
3. **Queue Jobs:** For very large cleanups, consider queuing the deletion
4. **Off-Peak Hours:** Schedule cleanup during low-traffic periods (default: 2 AM)

## FAQ

**Q: Can I recover deleted files?**
A: Files are soft-deleted in the database but physically removed from storage. The database records can be restored, but the actual files cannot.

**Q: What happens if cleanup fails?**
A: Failed deletions are logged. The `--without-overlapping` flag prevents concurrent executions.

**Q: Can I change the 7-day period?**
A: Yes, use the `--days` option: `php artisan files:cleanup --days=14`

**Q: Does this affect download limits or expiration dates?**
A: No, this cleanup is based solely on upload time (`created_at` timestamp).

**Q: How do I know cleanup is working?**
A: Check the activity logs in the admin panel or run `php artisan files:cleanup --dry-run` to see what would be deleted.
