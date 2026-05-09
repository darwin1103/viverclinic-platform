# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

### Development
- Build assets: `npm run build`
- Install PHP dependencies: `composer install`
- Install JS dependencies: `npm install`
- Generate app key: `php artisan key:generate`
- Run migrations and seeders: `php artisan migrate --seed`

### Testing
- Run all tests: `php artisan test` or `vendor/bin/phpunit`
- Run a single test: `php artisan test tests/Feature/ExampleTest.php`

## Architecture & Structure

ViverClinic is a multi-branch clinic management platform built with Laravel 12.

### Core Architecture
- **Multi-Branching**: Implemented via `BranchScope` in `app/Models/Scopes/BranchScope.php` to ensure data isolation between clinics.
- **Role-Based Access Control (RBAC)**: Uses Spatie Laravel-Permission. Roles include `SUPER_ADMIN`, `OWNER`, `ADMIN`, `EMPLOYEE`, `SALES`, and `PATIENT`.
- **Route Organization**: Routes are split by user role for clarity:
    - `routes/admin.php`: Administrative and managerial functions.
    - `routes/staff.php`: Clinical and operational staff functions.
    - `routes/client.php`: Patient portal functions.
    - `routes/web.php`: Main entry and shared routes.

### Directory Layout
- `app/Http/Controllers/`: Divided by role (`Admin/`, `Staff/`, `Client/`, `Auth/`).
- `app/Models/`: Eloquent models. Note the `Scopes` directory for multi-branch logic.
- `app/Traits/`: Modular logic shared across models.
- `resources/views/`: Blade templates organized by user role (`admin/`, `staff/`, `client/`).

### Key Business Logic
- **Inventory & Treatment**: Detailed tracking of laser shots and treatment packages.
- **Referral System**: Automated loyalty rewards for patients inviting new clients.
- **Financials**: Integrated accounting for payments, expenses, and installments.
