# Installation Guide - ERP System

## Prerequisites

Before installing, ensure you have:

- **PHP 8.0 or higher** with the following extensions:
  - PDO
  - pdo_mysql
  - mbstring
  - session
- **MySQL 5.7 or higher** (or MariaDB 10.2+)
- **Apache or Nginx** web server with mod_rewrite enabled
- **Composer** (optional, but recommended)

## Installation Steps

### 1. Clone or Download the Project

```bash
git clone <repository-url> erpsins
cd erpsins
```

### 2. Set Up Environment Variables

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure your database settings:

```env
# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=erpsins
DB_USER=your_database_user
DB_PASS=your_database_password

# Application Settings
APP_NAME="ERP System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.com

# Session Settings
SESSION_LIFETIME=7200
SESSION_NAME=erp_session

# Security
APP_KEY=your-randomly-generated-secret-key-here
```

**IMPORTANT:** Generate a secure random key for `APP_KEY`:

```bash
openssl rand -base64 32
```

### 3. Create Database

Login to MySQL:

```bash
mysql -u root -p
```

Create the database:

```sql
CREATE DATABASE erpsins CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 4. Import Database Schema

Import the database schema:

```bash
mysql -u your_user -p erpsins < database/schema.sql
```

This will create all necessary tables and populate them with sample data.

### 5. Set Permissions

Make sure the web server can write to specific directories:

```bash
chmod -R 755 public/
chmod -R 777 logs/ tmp/ cache/ public/uploads/
```

If you're on a shared hosting environment, you might need different permissions (e.g., 755 instead of 777).

### 6. Configure Web Server

#### Apache

If using Apache, the `.htaccess` file is already configured in the `public/` directory.

Make sure your Apache configuration has:

```apache
<Directory /path/to/erpsins/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Your DocumentRoot should point to the `public/` directory:

```apache
DocumentRoot /path/to/erpsins/public
```

#### Nginx

For Nginx, add this to your server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/erpsins/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7. Install Dependencies (Optional)

If you have Composer installed:

```bash
composer install --optimize-autoloader --no-dev
```

### 8. Restart Web Server

```bash
# Apache
sudo systemctl restart apache2

# Nginx
sudo systemctl restart nginx
```

## First Access

1. Open your browser and navigate to your domain or `http://localhost`

2. You will be redirected to the login page

3. Use one of these demo credentials:

   **Admin Account:**
   - Email: `admin@example.com`
   - Password: `admin123`

   **Manager Account:**
   - Email: `manager@example.com`
   - Password: `admin123`

   **User Account:**
   - Email: `user@example.com`
   - Password: `admin123`

**⚠️ IMPORTANT SECURITY NOTE:**

After logging in for the first time:
1. Change the default passwords immediately!
2. Delete or modify the demo users
3. Update the `APP_KEY` in your `.env` file
4. Set `APP_DEBUG=false` in production
5. Consider deleting the sample data from the database

## Post-Installation

### Change Default Passwords

1. Login as admin
2. Go to Settings → Users
3. Change passwords for all default accounts

### Configure Company Settings

1. Login as admin
2. Go to Settings
3. Update your company information

### Backup Database

Set up regular database backups:

```bash
# Create a backup
mysqldump -u your_user -p erpsins > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u your_user -p erpsins < backup_20251110.sql
```

## Troubleshooting

### Database Connection Error

- Check your `.env` file database credentials
- Ensure MySQL service is running: `sudo systemctl status mysql`
- Test connection: `mysql -u your_user -p -h localhost`

### Permission Errors

```bash
# Fix ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data /path/to/erpsins

# Fix permissions
sudo chmod -R 755 /path/to/erpsins
sudo chmod -R 777 /path/to/erpsins/logs
sudo chmod -R 777 /path/to/erpsins/tmp
sudo chmod -R 777 /path/to/erpsins/cache
sudo chmod -R 777 /path/to/erpsins/public/uploads
```

### 404 Errors on All Pages

- Check that `mod_rewrite` is enabled (Apache)
- Verify `.htaccess` file exists in `public/` directory
- Check web server configuration points to `public/` directory

### Blank White Page

- Enable error display temporarily in `.env`:
  ```
  APP_DEBUG=true
  ```
- Check PHP error logs
- Ensure all required PHP extensions are installed

## Updating

To update the application:

1. Backup your database
2. Backup your `.env` file
3. Pull the latest changes
4. Run any new migrations
5. Clear cache: `rm -rf cache/*`

## Support

For issues and questions:
- Check the README.md file
- Review the documentation
- Create an issue on GitHub

## License

MIT License
