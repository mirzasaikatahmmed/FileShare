# 🚀 File Sharing Application

A simple, secure file sharing platform built with Laravel. Upload files anonymously, protect them with passwords, and share via short URLs or QR codes!

## ✨ Features

- 📁 **Anonymous Upload** - No login required to upload files
- 🔒 **Password Protected** - Secure files with custom passwords
- 📱 **QR Code Sharing** - Auto-generated QR codes for easy sharing
- 🔗 **Short URLs** - Clean, memorable links
- 📊 **Download Tracking** - Monitor file downloads
- ⏰ **Auto Expiration** - Set expiration dates for files
- 📈 **Download Limits** - Control how many times files can be downloaded
- 🗑️ **Automatic Cleanup** - Files auto-delete after 7 days (configurable)
- 👨‍💼 **Admin Dashboard** - Complete analytics and file management
- 🎨 **Modern UI** - Beautiful, responsive design with Tailwind CSS

## 🛠️ Quick Setup

### Option 1: Automated Setup (Recommended)
```bash
# Run the setup script
setup.bat
```

### Option 2: Manual Setup

**1. Create Database**
```sql
CREATE DATABASE file_sharing;
```

**2. Run Migrations & Seeds**
```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=SettingSeeder
```

**3. Setup Storage**
```bash
php artisan storage:link
```

**4. Start Server**
```bash
php artisan serve
```

Visit: **http://localhost:8000**

## 🎯 How It Works

### 1. Upload a File
- Drag & drop or select a file
- Enter title and password
- Optional: Set expiration or download limit

### 2. Get Share Links
- Short URL (e.g., `http://localhost:8000/f/abc123`)
- QR Code image (downloadable)

### 3. Share & Download
- Recipients visit link or scan QR
- Enter password to unlock
- Preview and download file

## 📁 What's Inside

```
✅ Core Backend
   ├─ File upload with validation
   ├─ Password protection (bcrypt)
   ├─ Short URL generation (Hashids)
   ├─ QR code generation
   └─ Download tracking

✅ Database
   ├─ Files table (with expiration, limits)
   ├─ Downloads table (tracking)
   ├─ Activity logs
   └─ Settings

✅ Frontend Views
   ├─ Homepage (upload form)
   ├─ Success page (URL + QR code)
   ├─ Password entry page
   └─ File preview & download

✅ Services
   ├─ FileService (upload/download)
   ├─ QrCodeService (QR generation)
   └─ ShortUrlService (URL shortening)
```

## ⚙️ Configuration

Edit `.env`:
```env
APP_NAME="File Share"
DB_DATABASE=file_sharing
DB_USERNAME=root
DB_PASSWORD=
```

Edit `config/file-sharing.php` for:
- Max file size
- Allowed file types
- Expiration settings
- Rate limits

## 🚀 Tech Stack

- Laravel 11
- MySQL
- Tailwind CSS
- SimpleSoftwareIO QR Code
- Vinkla Hashids

## 🐛 Troubleshooting

**QR codes not showing?**
```bash
php artisan storage:link
```

**Routes not working?**
```bash
php artisan route:clear
php artisan cache:clear
```

**Database errors?**
- Start XAMPP MySQL
- Check database exists
- Verify `.env` credentials

## 🗑️ Automatic File Cleanup

Files are **automatically deleted after 7 days** from upload time.

**Manual Cleanup:**
```bash
# Test what would be deleted (dry run)
php artisan files:cleanup --dry-run

# Delete expired files now
php artisan files:cleanup

# Custom cleanup period
php artisan files:cleanup --days=14
```

**Production Setup:**
Add to crontab (Linux/Ubuntu):
```cron
* * * * * cd /path/to/FileShare && php artisan schedule:run >> /dev/null 2>&1
```

For detailed setup instructions, see `AUTOMATIC-CLEANUP.md`.

## 👨‍💼 Admin Panel

Access the admin dashboard at `http://localhost:8000/admin`

**Default Credentials:**
- Email: `admin@fileshare.com`
- Password: `password`

**Features:**
- 📊 Dashboard with statistics and trends
- 📁 File management (search, filter, bulk delete)
- 📈 Analytics (uploads, downloads, storage)
- 📝 Activity logs with filtering

## 📖 Documentation

See these files for complete documentation:
- `SETUP-GUIDE.md` - Installation and setup
- `AUTOMATIC-CLEANUP.md` - File cleanup configuration
- `laravel-file-sharing-documentation.md` - Technical documentation

## 🎉 Ready!

Your file sharing application is ready to use! 🎊

Run `php artisan serve` and start sharing files!

---

Built with ❤️ using Laravel
