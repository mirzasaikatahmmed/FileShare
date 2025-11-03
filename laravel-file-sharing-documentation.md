# Laravel File Sharing Website Documentation

## Project Overview
A simple, secure anonymous file sharing platform built with Laravel that allows anyone to upload files with password protection, generate short encrypted URLs with QR codes, and share files easily. Admin dashboard provides analytics and monitoring capabilities.

---

## Table of Contents
1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [System Requirements](#system-requirements)
4. [Installation](#installation)
5. [Database Schema](#database-schema)
6. [Project Structure](#project-structure)
7. [Implementation Phases](#implementation-phases)
8. [User Features](#user-features)
9. [Admin Dashboard Features](#admin-dashboard-features)
10. [Security Features](#security-features)
11. [API Endpoints](#api-endpoints)
12. [File Storage Strategy](#file-storage-strategy)

---

## Features

### Public Features (No Authentication Required)
- **Homepage Upload Form**:
  - Single file upload
  - File title/description field
  - Password protection (required)
  - Drag and drop support
  - File validation (type, size)

- **After Upload**:
  - Short encrypted URL generation
  - QR code generation for easy sharing
  - Copy link button
  - Download QR code as image

- **File Download**:
  - Access via short URL or QR code scan
  - Password verification required
  - Download tracking
  - File preview (images, PDFs, videos)
  - Single/multiple download support

### Admin Dashboard Features
- **Analytics & Statistics**:
  - Total files uploaded
  - Total downloads count
  - Storage usage analytics
  - Daily/weekly/monthly upload trends
  - Popular file types
  - Download statistics
  - Geographic data (if available)

- **File Management**:
  - View all uploaded files
  - Search and filter files
  - Delete files (moderation)
  - View file details and analytics
  - Bulk operations

- **System Monitoring**:
  - Activity logs
  - Storage monitoring
  - System health status
  - Failed access attempts
  - Settings configuration

- **Reports**:
  - Generate reports (CSV, PDF)
  - Upload/download reports
  - Storage reports
  - User behavior analytics

---

## Tech Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL 8.0 or PostgreSQL
- **Cache**: Redis
- **Queue**: Redis/Database

### Frontend
- **Blade Templates** with Laravel Mix/Vite
- **CSS Framework**: Tailwind CSS / Bootstrap 5
- **JavaScript**: Alpine.js / Vue.js
- **Admin Template**: Laravel Breeze/Jetstream or AdminLTE

### Additional Tools
- **File Storage**: Laravel Storage (Local/S3/DigitalOcean Spaces)
- **Authentication**: Laravel Breeze (Admin only)
- **QR Code Generation**: SimpleSoftwareIO/simple-qrcode or BaconQrCode
- **URL Shortening**: Custom implementation with hashids
- **File Processing**: Intervention Image, FFmpeg (for video thumbnails)
- **PDF Generation**: DomPDF/Snappy (for reports)

---

## System Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional but recommended)
- Web Server (Apache/Nginx)
- Minimum 2GB RAM
- SSD storage recommended

---

## Installation

### 1. Create Laravel Project
```bash
composer create-project laravel/laravel file-sharing-app
cd file-sharing-app
```

### 2. Install Dependencies
```bash
# Install Laravel Breeze for admin authentication
composer require laravel/breeze --dev
php artisan breeze:install

# Install additional packages
composer require intervention/image
composer require simplesoftwareio/simple-qrcode
composer require vinkla/hashids
composer require spatie/laravel-permission

# For admin dashboard (optional)
composer require jeroennoten/laravel-adminlte
```

### 3. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="File Sharing Platform"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=file_sharing
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=local
# For production, use S3 or similar
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=

QUEUE_CONNECTION=database
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 4. Setup Database
```bash
php artisan migrate
php artisan db:seed
```

### 5. Install Frontend Dependencies
```bash
npm install
npm run dev
```

### 6. Setup Storage
```bash
php artisan storage:link
```

### 7. Run the Application
```bash
php artisan serve
```

---

## Database Schema

### Users Table (Admin Only)
```sql
- id (bigint, primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- role (enum: 'admin', default: 'admin')
- is_active (boolean, default: true)
- last_login_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Files Table
```sql
- id (bigint, primary key)
- title (string) # User-provided title
- original_name (string) # Original filename
- stored_name (string, unique) # UUID-based filename
- file_path (string) # Storage path
- file_size (bigint) # Size in bytes
- mime_type (string)
- extension (string)
- password (string) # Hashed password
- short_code (string, unique, index) # Short URL identifier (e.g., "abc123")
- qr_code_path (string, nullable) # Path to QR code image
- expires_at (timestamp, nullable) # Optional expiration
- max_downloads (integer, nullable) # Optional download limit
- downloads_count (integer, default: 0)
- ip_address (string) # Uploader IP
- user_agent (text, nullable) # Uploader browser info
- is_active (boolean, default: true)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable) # Soft deletes for admin
```

### Downloads Table
```sql
- id (bigint, primary key)
- file_id (foreign key -> files.id)
- ip_address (string)
- user_agent (text)
- country (string, nullable) # Geographic data
- city (string, nullable)
- downloaded_at (timestamp)
- created_at (timestamp)
```

### Activity_Logs Table
```sql
- id (bigint, primary key)
- file_id (foreign key -> files.id, nullable)
- action (string) # upload, download, delete, access_denied, etc.
- description (text)
- ip_address (string)
- user_agent (text)
- created_at (timestamp)
```

### Settings Table
```sql
- id (bigint, primary key)
- key (string, unique)
- value (text)
- type (enum: 'string', 'integer', 'boolean', 'json')
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── CleanExpiredFiles.php
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php           # Homepage & upload
│   │   ├── FileController.php           # File access & download
│   │   └── Admin/
│   │       ├── DashboardController.php  # Admin dashboard
│   │       ├── FileController.php       # File management
│   │       ├── AnalyticsController.php  # Statistics & reports
│   │       └── SettingsController.php   # System settings
│   ├── Middleware/
│   │   ├── IsAdmin.php
│   │   ├── TrackDownload.php
│   │   ├── CheckFileAccess.php
│   │   └── LogActivity.php
│   └── Requests/
│       ├── FileUploadRequest.php
│       └── FileAccessRequest.php
├── Models/
│   ├── User.php                         # Admin only
│   ├── File.php
│   ├── Download.php
│   ├── ActivityLog.php
│   └── Setting.php
├── Services/
│   ├── FileService.php                  # File upload/download logic
│   ├── QrCodeService.php                # QR code generation
│   ├── ShortUrlService.php              # Short URL generation
│   └── AnalyticsService.php             # Statistics calculation
└── Helpers/
    └── helpers.php                       # Global helper functions

database/
├── migrations/
│   ├── 2024_01_01_create_users_table.php
│   ├── 2024_01_02_create_files_table.php
│   ├── 2024_01_03_create_downloads_table.php
│   ├── 2024_01_04_create_activity_logs_table.php
│   └── 2024_01_05_create_settings_table.php
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── AdminSeeder.php
│   └── SettingSeeder.php
└── factories/

resources/
├── views/
│   ├── welcome.blade.php                # Homepage with upload form
│   ├── file/
│   │   ├── access.blade.php            # Password entry page
│   │   ├── preview.blade.php           # File preview & download
│   │   └── success.blade.php           # Upload success with QR code
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── files/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── analytics/
│   │   │   ├── index.blade.php
│   │   │   └── reports.blade.php
│   │   └── settings/
│   │       └── index.blade.php
│   └── layouts/
│       ├── app.blade.php                # Public layout
│       └── admin.blade.php              # Admin layout
├── css/
│   ├── app.css
│   └── admin.css
└── js/
    ├── app.js
    ├── upload.js                        # Drag & drop upload
    └── admin.js

routes/
├── web.php                               # Public routes
├── admin.php                             # Admin routes (auth required)
└── api.php                               # Optional API routes

public/
├── qrcodes/                              # Generated QR codes
└── storage -> ../storage/app/public

storage/
└── app/
    ├── private/
    │   └── files/                        # Uploaded files (private)
    └── public/
        └── qrcodes/                      # QR codes (public)

config/
├── filesystems.php
├── hashids.php                           # URL shortening config
└── file-sharing.php                      # Custom app config
```

---

## Implementation Phases

### Phase 1: Foundation Setup (Days 1-2)
- [ ] Laravel installation and configuration
- [ ] Database design and migrations
- [ ] Install required packages (QR code, hashids, etc.)
- [ ] Admin authentication setup (Laravel Breeze)
- [ ] Basic UI/UX setup with Tailwind CSS

### Phase 2: Homepage & Upload (Days 3-4)
- [ ] Design homepage with upload form
- [ ] Implement drag & drop file upload
- [ ] File validation (type, size)
- [ ] Create FileService for upload handling
- [ ] Generate short URLs with hashids
- [ ] Implement password hashing
- [ ] Generate QR codes
- [ ] Display upload success page with QR code

### Phase 3: File Access & Download (Days 5-6)
- [ ] Create file access page with password form
- [ ] Implement password verification
- [ ] File preview functionality (images, PDFs, videos)
- [ ] Secure file download system
- [ ] Download tracking implementation
- [ ] Activity logging
- [ ] Handle expired files
- [ ] Handle download limits

### Phase 4: Admin Dashboard (Days 7-9)
- [ ] Admin authentication and authorization
- [ ] Dashboard with key statistics
- [ ] File management interface
  - [ ] View all files
  - [ ] Search and filter
  - [ ] View file details
  - [ ] Delete files
- [ ] Analytics page
  - [ ] Upload/download charts
  - [ ] Popular files
  - [ ] Storage usage
  - [ ] Geographic data
- [ ] Activity logs viewer
- [ ] System settings management

### Phase 5: Advanced Features (Days 10-12)
- [ ] Rate limiting for uploads and downloads
- [ ] File expiration system
- [ ] Automatic cleanup of expired files (cron job)
- [ ] Download reports (CSV, PDF)
- [ ] File type icons and better preview
- [ ] Responsive design optimization
- [ ] Copy link to clipboard functionality
- [ ] Download QR code as image

### Phase 6: Security & Optimization (Days 13-14)
- [ ] Security hardening
  - [ ] CSRF protection
  - [ ] File type validation (whitelist)
  - [ ] Malware scanning (optional)
  - [ ] Rate limiting
  - [ ] IP blocking for abuse
- [ ] Performance optimization
  - [ ] Image optimization for QR codes
  - [ ] Caching for statistics
  - [ ] Database indexing
- [ ] Error handling and logging

### Phase 7: Testing & Deployment (Days 15-16)
- [ ] Feature testing
- [ ] Security testing
- [ ] Cross-browser testing
- [ ] Mobile responsiveness testing
- [ ] Load testing
- [ ] Documentation
- [ ] Deployment setup
- [ ] Backup strategy
- [ ] Monitoring setup

---

## Public User Flow

### 1. Homepage (No Auth Required)
```
Features:
- Clean, minimal design
- Upload form with:
  * File upload field (drag & drop support)
  * File title input field
  * Password input field (required)
  * Optional: Expiration time selector
  * Optional: Maximum downloads limit
- File validation (type, size) before upload
- Upload progress bar
- File size and type indicators
```

### 2. Upload Process
```php
// Features:
- Drag and drop file upload
- Click to browse upload
- Real-time file validation
- Progress bar with percentage
- File size display
- Automatic file processing
- Generate unique short code
- Generate QR code automatically
- Hash password securely
```

### 3. Upload Success Page
```
Display after successful upload:
- Success message
- Short encrypted URL (e.g., https://domain.com/f/abc123)
- Large QR code for scanning
- "Copy Link" button (clipboard)
- "Download QR Code" button (as PNG)
- Upload another file button
- File details:
  * File title
  * Original filename
  * File size
  * Upload date
  * Expiration date (if set)
  * Max downloads (if set)
```

### 4. File Access Page
```
When accessing short URL:
- Display file title
- File icon/type indicator
- File size and upload date
- Password input form
- Submit button
- Error handling for:
  * Wrong password
  * Expired file
  * Download limit reached
  * File not found
```

### 5. File Preview & Download
```
After password verification:
- File preview (for supported types):
  * Images (jpg, png, gif, webp)
  * PDFs (embedded viewer)
  * Videos (HTML5 player)
  * Audio (HTML5 player)
- Download button (prominent)
- File information panel:
  * Title
  * Original filename
  * File size
  * Upload date
  * Downloads count
- Share QR code again
```

---

## Admin Dashboard Features

### 1. Dashboard Overview
```
Key Metrics (Cards):
- Total files uploaded (all time)
- Total downloads count
- Total storage used (GB/TB)
- Active files (not expired)
- Today's uploads
- Today's downloads

Charts & Graphs:
- Upload trend (last 30 days)
- Download trend (last 30 days)
- Storage usage over time
- File type distribution (pie chart)
- Top 10 most downloaded files
- Recent activity timeline
```

### 2. File Management
```
Features:
- List all files (paginated table)
- Columns:
  * ID
  * Title
  * Original filename
  * File size
  * Upload date
  * Downloads count
  * Status (active/expired)
  * Actions
- Search by title/filename
- Filter by:
  * Date range
  * File type
  * Status (active/expired)
  * Size range
- Sort by any column
- View individual file details:
  * All metadata
  * Download history
  * Access attempts log
  * QR code preview
  * Short URL
- Delete files (with confirmation)
- Bulk delete operations
- Download files
- Preview files
```

### 3. Analytics & Reports
```
Upload Analytics:
- Total uploads per day/week/month
- File size distribution
- File type statistics
- Peak upload times
- Average file size

Download Analytics:
- Total downloads per day/week/month
- Most downloaded files
- Download success/failure rate
- Peak download times
- Geographic distribution (if available)

Storage Analytics:
- Total storage used
- Storage growth rate
- Largest files
- Storage by file type
- Cleanup statistics (expired files)

Report Generation:
- Date range selector
- Export as CSV
- Export as PDF
- Schedule reports (optional)
```

### 4. Activity Logs
```
Log Events:
- File uploads (with IP, timestamp)
- File downloads (with IP, timestamp)
- File access attempts (successful/failed)
- Admin logins
- File deletions (by admin)
- Settings changes

Features:
- Real-time log viewer
- Filter by:
  * Action type
  * Date range
  * IP address
  * File ID
- Search logs
- Pagination
- Export logs (CSV)
```

### 5. System Settings
```
General Settings:
- Site name
- Site URL
- Maintenance mode toggle
- Contact email

Upload Settings:
- Max file size (MB)
- Allowed file types (whitelist)
- Blocked file types (blacklist)
- Default expiration time
- Max expiration time

Security Settings:
- Rate limiting (uploads per IP per hour)
- Rate limiting (downloads per IP per hour)
- Password strength requirements
- Enable/disable file upload
- IP whitelist/blacklist

Storage Settings:
- Storage limit warning threshold
- Auto-cleanup expired files
- Cleanup frequency
- Storage driver (local/S3)

QR Code Settings:
- QR code size
- QR code format (PNG/SVG)
- QR code error correction level
```

### 6. System Health
```
Monitoring:
- Disk space available
- Database size
- Total files count
- Queue status (if using queues)
- Cache status
- Last cleanup run time

Maintenance:
- Clear cache button
- Run cleanup manually
- Database optimization
- View error logs
- System information
```

---

## Security Features

### 1. Upload Security
- File type validation (whitelist approach)
- File extension validation
- MIME type verification
- File size limits
- Rate limiting per IP address
- CSRF protection on upload
- Honeypot field (anti-bot)
- File content validation
- Malware scanning (optional - ClamAV)

### 2. File Storage Security
- Files stored outside public directory
- Secure file naming (UUID-based)
- No direct file access via URL
- Private storage with Laravel Storage
- Separate storage for QR codes (public)
- File path obfuscation
- No directory listing

### 3. Access Security
- Password protection (required)
- Password hashing (bcrypt)
- Short code generation (secure random)
- No predictable URLs
- Password verification before download
- Failed attempt tracking
- IP-based rate limiting
- Temporary signed URLs for downloads

### 4. Link Security
- Unique short codes (hashids or random)
- Link expiration (optional)
- Download limit enforcement
- One-time passwords (optional feature)
- Link deactivation option
- No enumeration attacks

### 5. Data Protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Laravel Blade escaping)
- CSRF protection (all forms)
- HTTPS enforcement (production)
- Input sanitization
- Output encoding
- Secure headers (CSP, HSTS, X-Frame-Options)

### 6. Admin Security
- Admin-only authentication
- Strong password requirements
- Session timeout
- Admin activity logging
- IP whitelist option
- Two-factor authentication (optional)
- Failed login tracking
- Brute force protection

### 7. Monitoring & Logging
- All uploads logged (IP, timestamp, file info)
- All downloads logged
- Failed access attempts logged
- Admin actions logged
- Suspicious activity detection
- Automated alerts (optional)
- Log retention policy

---

## API Endpoints

### Public Routes (No Authentication)

#### Homepage
```
GET    /                             # Homepage with upload form
```

#### File Upload
```
POST   /upload                       # Upload file with title & password
       Request: multipart/form-data
       - file: file (required)
       - title: string (required)
       - password: string (required, min:6)
       - expires_at: datetime (optional)
       - max_downloads: integer (optional)

       Response: JSON
       - short_code: string
       - short_url: string
       - qr_code_url: string
       - file_id: integer
```

#### File Access
```
GET    /f/{short_code}               # File access page (password form)
POST   /f/{short_code}/verify        # Verify password
       Request: JSON/Form
       - password: string (required)

       Response: Redirect to preview or JSON error

GET    /f/{short_code}/preview       # File preview (after password verification)
       Session-based authentication
```

#### File Download
```
GET    /f/{short_code}/download      # Download file (after password verification)
       Response: File download (with tracking)
```

#### QR Code
```
GET    /qr/{short_code}.png          # Get QR code image
```

---

### Admin Routes (Authentication Required)

#### Authentication
```
GET    /admin/login                  # Admin login page
POST   /admin/login                  # Admin login
POST   /admin/logout                 # Admin logout
```

#### Dashboard
```
GET    /admin                        # Admin dashboard
GET    /admin/dashboard              # Same as above
GET    /admin/dashboard/stats        # Get dashboard statistics (AJAX)
```

#### File Management
```
GET    /admin/files                  # List all files (paginated)
GET    /admin/files/{id}             # View file details
DELETE /admin/files/{id}             # Delete file
POST   /admin/files/bulk-delete      # Bulk delete files
GET    /admin/files/{id}/preview     # Preview file
GET    /admin/files/{id}/download    # Download file
```

#### Analytics
```
GET    /admin/analytics              # Analytics page
GET    /admin/analytics/uploads      # Upload statistics (AJAX)
GET    /admin/analytics/downloads    # Download statistics (AJAX)
GET    /admin/analytics/storage      # Storage statistics (AJAX)
GET    /admin/reports/generate       # Generate report
       Query: start_date, end_date, format (csv/pdf)
```

#### Activity Logs
```
GET    /admin/logs                   # Activity logs page
GET    /admin/logs/data              # Get logs (AJAX, paginated)
       Query: action, date_from, date_to, ip_address
```

#### Settings
```
GET    /admin/settings               # Settings page
POST   /admin/settings               # Update settings
POST   /admin/settings/clear-cache   # Clear cache
POST   /admin/settings/cleanup       # Run manual cleanup
```

---

### Optional API Routes (JSON)

```
POST   /api/upload                   # API file upload
GET    /api/file/{short_code}        # Get file info (public)
POST   /api/file/{short_code}/verify # Verify password (returns token)
GET    /api/file/{short_code}/download # Download with token

# Admin API
POST   /api/admin/login              # API login (returns token)
GET    /api/admin/stats              # Dashboard stats
GET    /api/admin/files              # List files
DELETE /api/admin/files/{id}         # Delete file
GET    /api/admin/analytics          # Analytics data
```

---

## File Storage Strategy

### Development
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
    ],
]
```

### Production Options

#### Option 1: AWS S3
```php
'disks' => [
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'visibility' => 'private',
    ],
]
```

#### Option 2: DigitalOcean Spaces
```php
'disks' => [
    'spaces' => [
        'driver' => 's3',
        'key' => env('DO_SPACES_KEY'),
        'secret' => env('DO_SPACES_SECRET'),
        'endpoint' => env('DO_SPACES_ENDPOINT'),
        'region' => env('DO_SPACES_REGION'),
        'bucket' => env('DO_SPACES_BUCKET'),
    ],
]
```

### File Organization
```
storage/
└── app/
    └── private/
        └── files/
            ├── {user_id}/
            │   ├── {year}/
            │   │   ├── {month}/
            │   │   │   └── {hashed_filename}
            └── thumbnails/
                └── {file_id}/
                    ├── small.jpg
                    ├── medium.jpg
                    └── large.jpg
```

---

## Key Code Examples

### 1. Home Controller (Upload)
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use App\Services\QrCodeService;
use App\Services\ShortUrlService;

class HomeController extends Controller
{
    protected $fileService;
    protected $qrCodeService;
    protected $shortUrlService;

    public function __construct(
        FileService $fileService,
        QrCodeService $qrCodeService,
        ShortUrlService $shortUrlService
    ) {
        $this->fileService = $fileService;
        $this->qrCodeService = $qrCodeService;
        $this->shortUrlService = $shortUrlService;
    }

    public function index()
    {
        return view('welcome');
    }

    public function upload(FileUploadRequest $request)
    {
        try {
            // Upload file
            $file = $this->fileService->upload($request);

            // Generate short URL code
            $shortCode = $this->shortUrlService->generate($file->id);
            $file->update(['short_code' => $shortCode]);

            // Generate QR code
            $qrCodePath = $this->qrCodeService->generate(
                route('file.access', $shortCode),
                $shortCode
            );
            $file->update(['qr_code_path' => $qrCodePath]);

            // Log activity
            activity()
                ->performedOn($file)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('file_uploaded');

            return view('file.success', [
                'file' => $file,
                'shortUrl' => route('file.access', $shortCode),
                'qrCodeUrl' => asset('storage/' . $qrCodePath)
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

### 2. File Service
```php
<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function upload($request)
    {
        $uploadedFile = $request->file('file');
        $title = $request->input('title');
        $password = $request->input('password');
        $expiresAt = $request->input('expires_at');
        $maxDownloads = $request->input('max_downloads');

        // Generate unique filename
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedName = Str::uuid() . '.' . $extension;

        // Create storage path
        $path = sprintf(
            'files/%s/%s/%s',
            date('Y'),
            date('m'),
            $storedName
        );

        // Store file privately
        Storage::disk('private')->put($path, file_get_contents($uploadedFile));

        // Create database record
        $file = File::create([
            'title' => $title,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'file_path' => $path,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'extension' => $extension,
            'password' => bcrypt($password),
            'expires_at' => $expiresAt,
            'max_downloads' => $maxDownloads,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $file;
    }

    public function download(File $file)
    {
        // Track download
        $file->increment('downloads_count');

        // Log download
        $file->downloads()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return Storage::disk('private')->download(
            $file->file_path,
            $file->original_name
        );
    }

    public function verifyPassword(File $file, string $password): bool
    {
        return \Hash::check($password, $file->password);
    }

    public function canDownload(File $file): array
    {
        // Check if expired
        if ($file->expires_at && $file->expires_at->isPast()) {
            return ['success' => false, 'message' => 'This file has expired'];
        }

        // Check download limit
        if ($file->max_downloads && $file->downloads_count >= $file->max_downloads) {
            return ['success' => false, 'message' => 'Download limit reached'];
        }

        // Check if active
        if (!$file->is_active) {
            return ['success' => false, 'message' => 'This file is no longer available'];
        }

        return ['success' => true];
    }
}
```

### 3. File Controller (Access & Download)
```php
<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function access($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if can download
        $check = $this->fileService->canDownload($file);
        if (!$check['success']) {
            abort(410, $check['message']);
        }

        return view('file.access', compact('file'));
    }

    public function verify(Request $request, $shortCode)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Verify password
        if (!$this->fileService->verifyPassword($file, $request->password)) {
            // Log failed attempt
            activity()
                ->performedOn($file)
                ->withProperties(['ip' => $request->ip()])
                ->log('access_denied');

            return back()->withErrors(['password' => 'Invalid password']);
        }

        // Store verification in session
        session(['verified_file_' . $file->id => true]);

        return redirect()->route('file.preview', $shortCode);
    }

    public function preview($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if verified
        if (!session('verified_file_' . $file->id)) {
            return redirect()->route('file.access', $shortCode);
        }

        return view('file.preview', compact('file'));
    }

    public function download($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if verified
        if (!session('verified_file_' . $file->id)) {
            abort(403, 'Unauthorized');
        }

        // Check if can download
        $check = $this->fileService->canDownload($file);
        if (!$check['success']) {
            abort(410, $check['message']);
        }

        return $this->fileService->download($file);
    }
}
```

### 4. QR Code Service
```php
<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function generate(string $url, string $filename): string
    {
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($url);

        $path = 'qrcodes/' . $filename . '.png';

        Storage::disk('public')->put($path, $qrCode);

        return $path;
    }
}
```

### 5. Short URL Service
```php
<?php

namespace App\Services;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function generate(int $fileId): string
    {
        // Option 1: Using Hashids
        return Hashids::encode($fileId);

        // Option 2: Random string (more secure)
        // return Str::random(8);
    }

    public function decode(string $shortCode): ?int
    {
        // Option 1: Using Hashids
        $decoded = Hashids::decode($shortCode);
        return $decoded[0] ?? null;

        // Option 2: If using random string, query database
        // $file = File::where('short_code', $shortCode)->first();
        // return $file ? $file->id : null;
    }
}
```

### 6. File Upload Request
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true; // No auth required
    }

    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'max:' . config('file-sharing.upload.max_file_size'), // KB
                'mimes:' . implode(',', config('file-sharing.upload.allowed_extensions'))
            ],
            'title' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:50',
            'expires_at' => 'nullable|date|after:now',
            'max_downloads' => 'nullable|integer|min:1|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Please select a file to upload',
            'file.max' => 'File size must not exceed ' . (config('file-sharing.upload.max_file_size') / 1024) . 'MB',
            'title.required' => 'Please provide a title for your file',
            'password.required' => 'Password is required to protect your file',
            'password.min' => 'Password must be at least 6 characters',
        ];
    }
}
```

---

## Configuration File

### config/file-sharing.php
```php
<?php

return [
    // Upload Configuration
    'upload' => [
        'max_file_size' => env('MAX_FILE_SIZE', 100 * 1024), // 100MB in KB
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'zip', 'rar', '7z',
            'mp4', 'avi', 'mov', 'wmv', 'flv',
            'mp3', 'wav', 'ogg', 'flac'
        ],
        'blocked_extensions' => [
            'exe', 'bat', 'cmd', 'sh', 'php', 'php3', 'php4', 'php5',
            'phtml', 'js', 'html', 'htm', 'scr', 'vbs', 'jar'
        ],
        'mime_types' => [
            // Images
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            // Archives
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed',
            // Videos
            'video/mp4', 'video/x-msvideo', 'video/quicktime',
            // Audio
            'audio/mpeg', 'audio/wav', 'audio/ogg',
        ],
    ],

    // Storage Configuration
    'storage' => [
        'disk' => env('FILESYSTEM_DISK', 'local'),
        'private_disk' => 'private', // For uploaded files
        'public_disk' => 'public',   // For QR codes
        'max_total_storage' => env('MAX_TOTAL_STORAGE', 100 * 1024 * 1024 * 1024), // 100GB
    ],

    // File Sharing Configuration
    'sharing' => [
        'default_expiration_days' => env('DEFAULT_EXPIRATION_DAYS', 7),
        'max_expiration_days' => env('MAX_EXPIRATION_DAYS', 30),
        'min_password_length' => env('MIN_PASSWORD_LENGTH', 6),
        'max_password_length' => env('MAX_PASSWORD_LENGTH', 50),
        'short_code_length' => env('SHORT_CODE_LENGTH', 8),
    ],

    // QR Code Configuration
    'qrcode' => [
        'size' => env('QRCODE_SIZE', 300),
        'format' => env('QRCODE_FORMAT', 'png'), // png or svg
        'error_correction' => env('QRCODE_ERROR_CORRECTION', 'H'), // L, M, Q, H
        'margin' => env('QRCODE_MARGIN', 2),
    ],

    // Rate Limiting
    'rate_limit' => [
        'uploads_per_ip_per_hour' => env('RATE_LIMIT_UPLOADS', 10),
        'downloads_per_ip_per_hour' => env('RATE_LIMIT_DOWNLOADS', 50),
        'access_attempts_per_file' => env('RATE_LIMIT_ACCESS_ATTEMPTS', 5),
    ],

    // Cleanup Configuration
    'cleanup' => [
        'auto_cleanup' => env('AUTO_CLEANUP_ENABLED', true),
        'cleanup_frequency' => env('CLEANUP_FREQUENCY', 'daily'), // hourly, daily, weekly
        'delete_expired_files' => env('DELETE_EXPIRED_FILES', true),
        'retention_days' => env('FILE_RETENTION_DAYS', 30), // Keep files for X days minimum
    ],

    // Security
    'security' => [
        'enable_malware_scan' => env('ENABLE_MALWARE_SCAN', false),
        'max_failed_attempts' => env('MAX_FAILED_ATTEMPTS', 5),
        'block_duration' => env('BLOCK_DURATION', 60), // minutes
    ],
];
```

### config/hashids.php
```php
<?php

return [
    'default' => [
        'salt' => env('HASHIDS_SALT', env('APP_KEY')),
        'length' => env('HASHIDS_LENGTH', 8),
        'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
    ],
];
```

### config/filesystems.php (additions)
```php
'disks' => [
    // ... existing disks ...

    'private' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'visibility' => 'private',
    ],

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## User Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                         PUBLIC USER                          │
└─────────────────────────────────────────────────────────────┘

1. UPLOAD FLOW:
   Homepage (/)
   ↓
   Fill Form (file + title + password)
   ↓
   POST /upload
   ↓
   [Server Processing]
   - Save file to storage
   - Hash password
   - Generate short code
   - Generate QR code
   ↓
   Success Page
   - Display short URL
   - Display QR code
   - Copy link button
   - Download QR button

2. ACCESS FLOW:
   Visit /f/{short_code}
   ↓
   Password Entry Page
   ↓
   POST /f/{short_code}/verify
   ↓
   [Server Verification]
   - Check password
   - Check expiration
   - Check download limit
   ↓
   If Valid → Preview Page
   - Show file details
   - Preview (if supported)
   - Download button
   ↓
   GET /f/{short_code}/download
   ↓
   [Server Processing]
   - Track download
   - Log activity
   ↓
   File Download

┌─────────────────────────────────────────────────────────────┐
│                           ADMIN                              │
└─────────────────────────────────────────────────────────────┘

Login → Dashboard
        ↓
        ├─ View Statistics
        ├─ Manage Files (view/delete)
        ├─ View Analytics
        ├─ View Activity Logs
        └─ Configure Settings
```

---

## Next Steps

### 1. Setup Development Environment
```bash
# Install PHP 8.1+, Composer, Node.js, MySQL
# Create Laravel project
composer create-project laravel/laravel file-sharing-app
cd file-sharing-app

# Install packages
composer require laravel/breeze --dev
composer require simplesoftwareio/simple-qrcode
composer require vinkla/hashids
composer require intervention/image
composer require spatie/laravel-permission

php artisan breeze:install
npm install && npm run dev
```

### 2. Database Setup
```bash
# Configure .env file
# Create database
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

### 3. Follow Implementation Phases
- **Phase 1**: Foundation (database, auth, UI)
- **Phase 2**: Upload functionality (homepage, file upload, QR generation)
- **Phase 3**: Access & download (password verification, preview, download)
- **Phase 4**: Admin dashboard (analytics, file management)
- **Phase 5**: Advanced features (rate limiting, expiration, cleanup)
- **Phase 6**: Security hardening
- **Phase 7**: Testing & deployment

### 4. Testing Strategy
- Test file uploads (various types, sizes)
- Test password protection
- Test QR code generation
- Test download tracking
- Test expiration and download limits
- Test admin dashboard features
- Security testing (SQL injection, XSS, file upload vulnerabilities)
- Load testing (concurrent uploads/downloads)

### 5. Deployment Preparation
- Choose hosting (VPS, shared hosting, cloud)
- Configure production environment
- Setup HTTPS (SSL certificate)
- Configure cloud storage (optional: S3, DigitalOcean Spaces)
- Setup cron jobs for cleanup
- Configure backups
- Setup monitoring (Laravel Telescope, Sentry)
- Configure mail for notifications

---

## Quick Start Commands

```bash
# Development
php artisan serve
npm run dev

# Create admin user
php artisan make:seeder AdminSeeder
php artisan db:seed --class=AdminSeeder

# Storage setup
php artisan storage:link
mkdir -p storage/app/private/files
mkdir -p storage/app/public/qrcodes

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run cleanup (manually)
php artisan files:cleanup

# Schedule (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Useful Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel File Storage](https://laravel.com/docs/filesystem)
- [Simple QR Code](https://www.simplesoftware.io/#/docs/simple-qrcode)
- [Hashids](https://hashids.org/)
- [Intervention Image](http://image.intervention.io/)
- [Tailwind CSS](https://tailwindcss.com/)
- [AdminLTE Template](https://adminlte.io)

---

## Common Issues & Solutions

### Issue: QR codes not displaying
**Solution**: Run `php artisan storage:link` and ensure public/storage symlink exists

### Issue: Files not uploading
**Solution**: Check upload_max_filesize and post_max_size in php.ini

### Issue: Short URLs not working
**Solution**: Ensure mod_rewrite is enabled and .htaccess is configured

### Issue: Permission denied errors
**Solution**: `chmod -R 775 storage bootstrap/cache`

---

## License
This documentation is provided as-is for educational purposes.

---

**Last Updated**: 2025-11-03
**Version**: 2.0 - Simplified Anonymous File Sharing
