# GetSoluce2

This repository contains a minimal Laravel application used for HR management examples.

## Requirements
- PHP 8.1+
- Composer
- SQLite or another supported database

## Setup
1. Clone the repository and install dependencies:
   ```bash
   composer install
   ```
2. Copy `.env.example` to `.env` and adjust database settings.
3. Generate an application key:
   ```bash
   php artisan key:generate
   ```
4. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

The included seeders will create a demo enterprise, a creator account, an admin,
and a sample absence record. You can run seeders separately with:
```bash
php artisan db:seed
```

## Running
Start the development server with:
```bash
php artisan serve
```
Then visit `http://localhost:8000`.
