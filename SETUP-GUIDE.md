# File Sharing Application - Setup Guide

## What Has Been Created

### ✅ Completed:
1. **Laravel Installation** - Fresh Laravel 11 installation
2. **Dependencies** - All required packages installed:
   - laravel/breeze (admin auth)
   - simplesoftwareio/simple-qrcode (QR code generation)
   - vinkla/hashids (URL shortening)
   - intervention/image (image processing)
   - spatie/laravel-permission (roles)
   - spatie/laravel-activitylog (activity logging)

3. **Database Migrations**:
   - `files` table
   - `downloads` table
   - `activity_logs` table
   - `settings` table

4. **Models**:
   - File.php (with relationships and helper methods)
   - Download.php
   - ActivityLog.php
   - Setting.php

5. **Services**:
   - FileService.php (upload, download, delete, password verification)
   - QrCodeService.php (QR code generation)
   - ShortUrlService.php (short URL generation)

6. **Controllers**:
   - HomeController.php (homepage, upload)
   - FileController.php (access, verify, preview, download)

7. **Configuration**:
   - config/file-sharing.php (all app settings)
   - config/hashids.php (hashids configuration)
   - config/filesystems.php (updated with private disk)

8. **Validation**:
   - FileUploadRequest.php

9. **Routes**:
   - Public routes (upload, file access)
   - Admin routes placeholder

---

## Next Steps to Complete

### 1. Database Setup

```bash
# Configure your .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_sharing
DB_USERNAME=root
DB_PASSWORD=

# Create the database
mysql -u root -p
CREATE DATABASE file_sharing;
exit;

# Run migrations
php artisan migrate
```

### 2. Storage Setup

```bash
# Create storage directories
mkdir -p storage/app/private/files
mkdir -p storage/app/public/qrcodes

# Create symbolic link
php artisan storage:link
```

### 3. Install Breeze for Admin Authentication

```bash
php artisan breeze:install blade
npm install
npm run dev
```

### 4. Create Admin Seeder

Create `database/seeders/AdminSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@fileshare.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
```

Run it:
```bash
php artisan db:seed --class=AdminSeeder
```

### 5. Create Views

You need to create the following Blade views in `resources/views/`:

#### `welcome.blade.php` - Homepage with upload form
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Sharing - Upload & Share Files Securely</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">File Share</h1>
                <p class="mt-2 text-gray-600">Upload and share files securely with password protection</p>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">File</label>
                        <input type="file" name="file" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        @error('file')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                            placeholder="Enter file title">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                            placeholder="Enter password to protect file">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-blue-600 text-white rounded-md py-2 px-4 hover:bg-blue-700">
                        Upload File
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
```

#### Create `resources/views/file/` directory and add:

1. **`file/success.blade.php`** - Upload success page with QR code
2. **`file/access.blade.php`** - Password entry page
3. **`file/preview.blade.php`** - File preview and download page

### 6. Run the Application

```bash
# Start development server
php artisan serve

# In another terminal, compile assets
npm run dev
```

Visit: http://localhost:8000

### 7. Test the Flow

1. Upload a file with title and password
2. You'll get a short URL and QR code
3. Visit the short URL
4. Enter password
5. Preview and download the file

---

## Additional Features to Implement

### Admin Dashboard
- Create Admin controllers
- Create admin views
- Add file management
- Add analytics dashboard

### Security Enhancements
- Add rate limiting middleware
- Add CAPTCHA for uploads
- Add malware scanning

### Advanced Features
- File expiration cron job
- Email notifications
- Bulk operations
- Advanced analytics

---

## Troubleshooting

### QR Codes not displaying
```bash
php artisan storage:link
```

### Permission errors
```bash
chmod -R 775 storage bootstrap/cache
```

### Composer errors
```bash
php composer.phar install
php composer.phar dump-autoload
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php ✅
│   │   ├── FileController.php ✅
│   │   └── Admin/ (TODO)
│   └── Requests/
│       └── FileUploadRequest.php ✅
├── Models/
│   ├── File.php ✅
│   ├── Download.php ✅
│   ├── ActivityLog.php ✅
│   └── Setting.php ✅
└── Services/
    ├── FileService.php ✅
    ├── QrCodeService.php ✅
    └── ShortUrlService.php ✅

config/
├── file-sharing.php ✅
├── hashids.php ✅
└── filesystems.php ✅ (updated)

database/
└── migrations/
    ├── create_files_table.php ✅
    ├── create_downloads_table.php ✅
    ├── create_activity_logs_table.php ✅
    └── create_settings_table.php ✅

routes/
└── web.php ✅ (updated)

resources/
└── views/
    ├── welcome.blade.php (TODO)
    └── file/
        ├── success.blade.php (TODO)
        ├── access.blade.php (TODO)
        └── preview.blade.php (TODO)
```

---

## Environment Variables

Add these to your `.env` file:

```env
APP_NAME="File Sharing"
APP_URL=http://localhost:8000

# File Upload Settings
MAX_FILE_SIZE=102400
DEFAULT_EXPIRATION_DAYS=7
MAX_EXPIRATION_DAYS=30
MIN_PASSWORD_LENGTH=6

# QR Code Settings
QRCODE_SIZE=300
QRCODE_FORMAT=png

# Rate Limiting
RATE_LIMIT_UPLOADS=10
RATE_LIMIT_DOWNLOADS=50

# Hashids
HASHIDS_LENGTH=8
```

---

## Quick Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list

# Create admin user
php artisan db:seed --class=AdminSeeder

# Run migrations fresh
php artisan migrate:fresh --seed
```

---

## What's Working Now

✅ Laravel fully installed and configured
✅ Database structure ready
✅ File upload logic complete
✅ Password protection system
✅ QR code generation
✅ Short URL system
✅ Download tracking
✅ Activity logging

## What Needs to Be Done

❌ Create Blade views
❌ Install and configure Tailwind CSS / Breeze
❌ Create admin dashboard
❌ Add file cleanup command
❌ Add middleware
❌ Add seeders
❌ Test complete flow

---

For detailed documentation, see: `laravel-file-sharing-documentation.md`
