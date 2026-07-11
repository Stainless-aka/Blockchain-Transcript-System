# Installation & Setup Guide

Complete step-by-step instructions for deploying the Blockchain-Based Student Transcript Verification System.

---

## Prerequisites

Before installation, ensure you have:

- **XAMPP** (or equivalent: WAMP, MAMP, LAMP) with:
  - PHP 8.0 or higher
  - MySQL 5.7+ / MariaDB 10+
  - Apache 2.4+
- **Modern Web Browser** (Chrome, Firefox, Edge, Safari)
- **Text Editor** (VS Code, Sublime, Notepad++ - optional for configuration)

---

## Step-by-Step Installation

### Step 1: Extract/Move the Project

1. Extract the `professional-starter-template` folder
2. Move it to your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\professional-starter-template\
   ```
   Or on Mac/Linux:
   ```
   /Applications/XAMPP/htdocs/professional-starter-template/
   ```

### Step 2: Configure the Database Connection

1. Open the `.env` file in the project root folder
2. Update these lines with your MySQL credentials:

```env
DB_HOST=localhost
DB_NAME=transcript_verification
DB_USER=root
DB_PASS=                    # Leave blank if no password, or enter your MySQL password
```

3. Update the application URL (adjust if using a different port):

```env
APP_URL=http://localhost/professional-starter-template/public
```

4. Save the file

### Step 3: Create the Database

#### Option A: Using phpMyAdmin (Recommended)

1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**
3. Open your browser and go to: `http://localhost/phpmyadmin`
4. Click on **"New"** in the left sidebar
5. Database name: `transcript_verification`
6. Collation: `utf8mb4_unicode_ci`
7. Click **"Create"**
8. Click on the newly created database
9. Click the **"Import"** tab
10. Click **"Choose File"**
11. Navigate to: `professional-starter-template/database/transcript_verification.sql`
12. Click **"Import"** at the bottom

#### Option B: Using Command Line

```bash
# Navigate to the database folder
cd C:\xampp\htdocs\professional-starter-template\database

# Import the SQL file
mysql -u root -p transcript_verification < transcript_verification.sql
```

### Step 4: Set Folder Permissions (Linux/Mac only)

If you're on Linux or Mac, set proper permissions:

```bash
cd /path/to/professional-starter-template
chmod -R 755 .
chmod -R 777 public/uploads
```

On Windows, XAMPP handles permissions automatically.

### Step 5: Start the Server

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**
4. Wait for both status indicators to turn green

### Step 6: Access the Application

Open your browser and navigate to:

```
http://localhost/professional-starter-template/public
```

You should see the **Login Page**.

---

## Default Login Credentials

| Field    | Value     |
|----------|-----------|
| Username | admin     |
| Password | admin123  |

> ⚠️ **Important:** Change the password immediately after first login via the Profile page!

---

## Testing the Installation

### 1. Test Login
- Use the credentials above to log in
- You should be redirected to the Dashboard

### 2. Test Student Management
- Go to **Students** → **Add Student**
- Fill in sample data and save
- Verify the student appears in the list

### 3. Test Transcript Creation (Blockchain)
- Go to **Transcripts** → **Create Transcript**
- Select a student, enter academic details
- Click **Create & Anchor to Blockchain**
- A new block should be added automatically

### 4. Test Blockchain Explorer
- Go to **Blockchain** menu
- You should see the Genesis Block (Index 0)
- Your new transcript block should appear
- Click **Validate Chain** — it should show "VALID"

### 5. Test Public Verification
- Go to **Verify Transcript** (or open in a new tab)
- Enter the Transcript ID from step 3
- You should see a green "VERIFIED" result

---

## Troubleshooting

### Issue: "Database connection failed"

**Solution:**
- Check MySQL is running in XAMPP Control Panel
- Verify `.env` credentials match your MySQL setup
- Try password blank if using default XAMPP (no password)
- Check database name is exactly `transcript_verification`

### Issue: "404 - Page Not Found" on every page

**Solution:**
- Ensure `.htaccess` exists in `public/` folder
- Enable Apache `mod_rewrite`:
  1. Open `C:\xampp\apache\conf\httpd.conf`
  2. Find line: `#LoadModule rewrite_module modules/mod_rewrite.so`
  3. Remove the `#` to uncomment it
  4. Save and restart Apache

### Issue: PDF upload not working

**Solution:**
- Check folder exists: `public/uploads/transcripts/`
- On Linux/Mac: `chmod 777 public/uploads/transcripts/`
- On Windows: Ensure the folder has write permissions

### Issue: "Class not found" errors

**Solution:**
- Run: `composer dump-autoload` (if Composer is installed)
- Or simply restart Apache

### Issue: Session errors or CSRF token failures

**Solution:**
- Clear browser cookies/cache
- Check PHP session directory is writable
- Restart Apache

---

## Optional: Using Composer

If you have Composer installed, run this in the project root:

```bash
composer install
composer dump-autoload
```

This is optional — the application works without Composer.

---

## URL Configuration (Custom Port or Domain)

### If XAMPP runs on a different port (e.g., 8080):

Update `.env`:
```env
APP_URL=http://localhost:8080/professional-starter-template/public
```

### If using a custom domain (e.g., transcript.local):

1. Edit `C:\Windows\System32\drivers\etc\hosts` (Windows) or `/etc/hosts` (Mac/Linux)
2. Add: `127.0.0.1 transcript.local`
3. Configure Apache Virtual Host
4. Update `.env`:
   ```env
   APP_URL=http://transcript.local
   ```

---

## Deployment to Production (Apache Server)

### 1. Upload Files
Upload all project files to your web server (via FTP/SFTP).

### 2. Update .env
```env
APP_ENV=production
APP_URL=https://yourdomain.com
DB_HOST=your_db_host
DB_NAME=transcript_verification
DB_USER=your_db_username
DB_PASS=your_db_password
```

### 3. Set Document Root
Point your Apache/Nginx document root to the `public/` folder.

### 4. Enable HTTPS
Use Let's Encrypt or your hosting provider's SSL certificate.

### 5. Security Hardening
- Remove all default/test accounts
- Change database passwords
- Set file permissions to 644 (files) and 755 (folders)
- Keep `public/uploads/` writable (777)

---

## Support

For issues, questions, or contributions:

- Check the main `README.md`
- Review database schema in `database/transcript_verification.sql`
- Examine routing in `routes/web.php`

---

**System Ready! 🚀**

Your Blockchain Transcript Verification System is now operational.
