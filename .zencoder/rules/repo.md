---
description: Repository Information Overview
alwaysApply: true
---

# IFMMS-ZAR Information

## Summary
Integrated Fleet Maintenance Management System (IFMMS) is a Laravel-based web application for managing vehicle fleets, maintenance schedules, and incident reporting. The system supports role-based access with Admin, Driver, and Technician roles, each with specific capabilities.

## Structure
- **app/**: Core application code (controllers, models, middleware)
- **bootstrap/**: Application bootstrapping files
- **config/**: Configuration files
- **database/**: Database migrations, seeders, and factories
- **public/**: Publicly accessible files (assets, index.php)
- **resources/**: Views, language files, and frontend assets
- **routes/**: Route definitions (web.php, api.php)
- **storage/**: Application storage (logs, cache, uploads)
- **tests/**: Test files (Unit and Feature tests)
- **vendor/**: Composer dependencies

## Language & Runtime
**Language**: PHP
**Version**: ^8.2
**Framework**: Laravel 12.x
**Build System**: Composer (PHP), Vite (JavaScript)
**Package Manager**: Composer (PHP), npm (JavaScript)

## Dependencies
**Main Dependencies**:
- laravel/framework: ^12.0
- laravel/tinker: ^2.10.1

**Development Dependencies**:
- fakerphp/faker: ^1.23
- laravel/pail: ^1.2.2
- laravel/pint: ^1.13
- laravel/sail: ^1.41
- mockery/mockery: ^1.6
- nunomaduro/collision: ^8.6
- phpunit/phpunit: ^11.5.3
- tailwindcss: ^4.0.0
- vite: ^6.0.11
- laravel-vite-plugin: ^1.2.0

## Build & Installation
```bash
# Install PHP dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run database migrations and seed
php artisan migrate
php artisan db:seed --class=MaintenanceSeeder

# Optional: Install and build frontend assets
npm install
npm run build

# Start the application
php artisan serve
```

## Testing
**Framework**: PHPUnit
**Test Location**: tests/ (Unit and Feature directories)
**Configuration**: phpunit.xml
**Run Command**:
```bash
php artisan test
```

## Main Entry Points
- **artisan**: Command-line interface for Laravel
- **public/index.php**: Main entry point for web requests
- **routes/web.php**: Web route definitions
- **routes/api.php**: API route definitions

## Features
- **Role-based Access Control**: Admin, Driver, and Technician roles
- **Vehicle Management**: Fleet tracking and assignment
- **Maintenance Scheduling**: Preventive maintenance planning
- **Incident Reporting**: Vehicle incident tracking and resolution
- **Analytics Dashboard**: Fleet health and maintenance metrics
- **Service Requests**: Driver-initiated maintenance requests
- **Route Management**: Vehicle route planning and optimization
- **Driver Performance**: Tracking and analysis of driver metrics
