# GetSoluce HR System

GetSoluce is a Laravel based multi-company HR management platform. It manages absences, advances, notes de frais and more. This repository contains the source code of the project.

## Requirements

- PHP >= 8.2
- Composer
- Laravel 11 or later
- A database driver supported by Laravel (SQLite database is provided by default)

## Setup

1. Clone this repository.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Copy the example environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Run the migrations (the `database/database.sqlite` file is already included):
   ```bash
   php artisan migrate
   ```
5. Seed the database with initial data or demo content:
   ```bash
   php artisan db:seed
   ```
   Alternatively you can use the custom command which also creates test accounts and sample data:
   ```bash
   php artisan hr:setup --fresh
   ```

## Running the Application

Start the development server with:
```bash
php artisan serve
```
Then visit `http://localhost:8000` in your browser and log in with the seeded test accounts.
