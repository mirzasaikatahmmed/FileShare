# Quick Start Guide - CasaOS Installation

Two ways to install the File Share application on CasaOS:

## Option 1: Automated Installation (Recommended)

### Step 1: Upload Files
Upload all application files to `/var/www/fileshare` on your CasaOS system via SFTP or Git.

### Step 2: Make Script Executable
```bash
cd /var/www/fileshare
sudo chmod +x install.sh
```

### Step 3: Run Installation Script
```bash
sudo ./install.sh
```

The script will prompt you for:
- Domain or IP address
- MySQL root password
- New database password
- Admin email
- Admin password

**That's it!** The script handles everything automatically.

---

## Option 2: Manual Installation

📖 **See [CASAOS-INSTALLATION.md](CASAOS-INSTALLATION.md)** for complete step-by-step instructions.

---

## Post-Installation

### Access Your Application

1. **Homepage:** `http://your-domain-or-ip`
2. **Admin Login:** `http://your-domain-or-ip/login`
3. **Admin Settings:** `http://your-domain-or-ip/admin/settings`

---

🎉 **Your File Share application is ready to use!**
