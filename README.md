# FloraFetch - Plants Ecommerce Website

FloraFetch is a PHP + MySQL based plants ecommerce website with separate customer and admin modules.  
It includes product browsing, cart, checkout, order tracking, reviews, wishlist, and admin management features.

## Features

### Customer
- Browse plants by category (`shop.php`)
- View plant details (`product.php`)
- Add/remove items in cart and wishlist
- Register/Login with profile image upload (`auth.php`)
- Place orders and track order status (`checkout.php`, `my_orders.php`)
- Submit product reviews

### Admin
- Admin dashboard with totals and recent orders (`admin/index.php`)
- Manage plants (`admin/manage_plants.php`)
- Manage orders and update statuses (`admin/manage_orders.php`)
- Manage users (`admin/manage_users.php`)
- Manage reviews (`admin/manage_reviews.php`)

## Tech Stack
- **Frontend:** HTML, CSS, Bootstrap, JavaScript
- **Backend:** Core PHP (procedural)
- **Database:** MySQL
- **Local Server:** XAMPP / Apache + MySQL

## Project Structure

```text
FloraFetch-Plants-Ecommerce-Website/
├── admin/                  # Admin module
├── assets/                 # CSS, JS, images, uploads
├── docs/                   # Project docs
├── includes/               # Shared PHP includes (db, header, footer)
├── *.php                   # Customer-side pages and actions
└── florafetch_db.sql       # Database schema + seed data
```

## Prerequisites
- PHP 8.x
- MySQL / MariaDB
- Apache server (XAMPP recommended)

## Local Setup

1. Clone/download this repository into your server directory (for XAMPP: `htdocs`).
2. Start **Apache** and **MySQL**.
3. Create/import database:
   - Open phpMyAdmin
   - Import `florafetch_db.sql`
4. Verify DB connection settings in:
   - `includes/db_connect.php`
   - Default values are:
     - Host: `localhost`
     - User: `root`
     - Password: *(empty)*
     - Database: `florafetch_db`
5. Open the project in browser:
   - `http://localhost/FloraFetch-Plants-Ecommerce-Website/`

## Default Admin Login

The SQL seed contains a default admin:
- **Email:** `admin@florafetch.com`
- **Password:** `admin123`

> Note: Password is stored in SQL as MD5 for compatibility with old data, and upgraded on login in auth flow.  
> For production usage, change credentials immediately.

## Core Pages
- `index.php` - Home
- `shop.php` - Shop
- `product.php` - Product details
- `cart.php` - Cart
- `checkout.php` - Checkout
- `my_orders.php` - User orders
- `profile.php` - User profile
- `admin/index.php` - Admin dashboard

## Database
Main tables:
- `users`
- `plants`
- `orders`
- `order_items`
- `reviews`
- `notifications`
- `user_addresses`

## Notes
- This project is designed for educational/demo use.
- Update DB credentials and strengthen security before production deployment.
