# ERP System - Modern PHP Application

A modern, full-featured ERP system built with PHP 8+, MySQL, and a beautiful UI powered by Tailwind CSS and Alpine.js.

## Features

- 🔐 **Authentication System** - Secure login, registration, and session management
- 📊 **Dashboard** - Real-time analytics and insights
- 👥 **User Management** - Role-based access control
- 📦 **Product Management** - Inventory and product catalog
- 👤 **Customer Management** - CRM functionality
- 🧾 **Invoice Management** - Create and track invoices
- 🎨 **Modern UI** - Responsive design with Tailwind CSS
- ⚡ **Fast & Efficient** - Optimized PHP 8+ with modern practices

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for autoloading)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd erpsins
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` and set your database credentials.

3. **Create database**
   ```bash
   mysql -u root -p
   CREATE DATABASE erpsins;
   ```

4. **Import database schema**
   ```bash
   mysql -u root -p erpsins < database/schema.sql
   ```

5. **Set permissions**
   ```bash
   chmod -R 755 public/
   chmod -R 777 logs/ tmp/ cache/ public/uploads/
   ```

6. **Install dependencies (optional)**
   ```bash
   composer install
   ```

7. **Access the application**
   - Point your web server to the `public/` directory
   - Visit: http://localhost

## Default Credentials

- **Email:** admin@example.com
- **Password:** admin123

**⚠️ Important:** Change these credentials immediately after first login!

## Project Structure

```
erpsins/
├── app/
│   ├── Controllers/     # Application controllers
│   ├── Models/          # Database models
│   └── Middleware/      # Middleware classes
├── core/
│   ├── Database.php     # Database connection
│   ├── Router.php       # URL routing
│   ├── Controller.php   # Base controller
│   └── Model.php        # Base model
├── config/              # Configuration files
├── database/            # SQL schemas and migrations
├── public/              # Public web root
│   ├── assets/          # CSS, JS, images
│   └── index.php        # Entry point
├── views/               # View templates
└── routes/              # Route definitions
```

## Security

- CSRF protection on all forms
- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with output escaping
- Session security with regeneration

## License

MIT License
