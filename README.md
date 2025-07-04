# GetSoluce2

GetSoluce2 is a simple Human Resource (HR) management application built with the Laravel framework. It provides basic features such as employee management, absence tracking and handling of payroll advances. The goal of this project is to demonstrate how a multi-tenant HR platform can be implemented using standard Laravel components.

## Setup

1. **Requirements**
   - PHP 8.1 or higher
   - Composer
   - SQLite (default) or any other database supported by Laravel

2. **Installation**
   ```bash
   # Install PHP dependencies
   composer install

   # Create your .env file (you can start from .env.example if present)
   php artisan key:generate

   # Configure the database (default uses database/database.sqlite)
   php artisan migrate
   ```

   Optionally, you can populate the application with test data using:
   ```bash
   php artisan hr:setup --fresh
   ```

## Running the Application

Start the built‑in PHP development server with:
```bash
php artisan serve
```

By default the server runs at `http://localhost:8000`. Log in using one of the test accounts created by `php artisan hr:setup` or register a new account.

