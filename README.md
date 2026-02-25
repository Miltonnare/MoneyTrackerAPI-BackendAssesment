# MoneyTracker API

A RESTful API for managing personal or business finances with support for multiple users, wallets, and transactions. Built with Laravel 12, this application provides robust financial tracking with features like balance management, transaction recording, and race condition prevention.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Database Schema](#database-schema)
- [Testing](#testing)
- [Development](#development)
- [License](#license)

## Overview

MoneyTracker API is a financial management system that allows:
- Multiple users to manage their finances independently
- Each user to have multiple wallets (e.g., "Business A Wallet", "Personal Savings")
- Recording income and expense transactions
- Real-time balance tracking with race condition protection
- Comprehensive validation and error handling

## Features

### Core Functionality
- **User Management**: Create and retrieve users with email validation
- **Wallet Management**: Create multiple wallets per user with unique names
- **Transaction Recording**: Add income/expense transactions with automatic balance updates
- **Balance Tracking**: Real-time wallet balance calculations with transaction history
- **Data Integrity**: Database-level constraints and pessimistic locking to prevent race conditions
- **Validation**: Comprehensive input validation with detailed error messages
- **Insufficient Funds Protection**: Prevents expense transactions that exceed wallet balance

### Technical Features
- RESTful API design with consistent JSON responses
- Database transactions with row-level locking
- Eloquent ORM relationships
- SQLite database (easily switchable to MySQL/PostgreSQL)
- Standardized error handling and HTTP status codes

## Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: SQLite (default), MySQL/PostgreSQL compatible
- **Package Manager**: Composer 2.x
- **Node.js**: 20.x+ (for asset compilation)
- **Testing**: PHPUnit 11.x

## System Requirements

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 20.x and npm
- SQLite3 extension (or MySQL/PostgreSQL for production)
- PHP Extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## Installation

### 1. Clone or Download the Project

```bash
git clone https://github.com/Miltonnare/MoneyTrackerAPI-BackendAssesment.git
cd MoneyTrackerAPI-BackendAssesment
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

#### For SQLite (Default)

Create the database file:

```bash
# On Windows (PowerShell)
New-Item -ItemType File -Path database/database.sqlite -Force

# On Unix/Linux/Mac
touch database/database.sqlite
```

#### For MySQL/PostgreSQL

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=money_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 7. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `users`
- `wallets`
- `transactions`
- `sessions` (for session management)

## Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME="MoneyTracker API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite - Default)
DB_CONNECTION=sqlite

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Database Configuration

The application uses SQLite by default for simplicity. For production environments, consider switching to MySQL or PostgreSQL by updating the `DB_CONNECTION` in your `.env` file.

## Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Alternative: Using Composer Scripts

Run the full development environment (server + queue + logs + vite):

```bash
composer run dev
```

## API Documentation

Base URL: `http://localhost:8000/api`

All responses follow this format:

```json
{
  "success": true|false,
  "message": "Description of the result",
  "data": { ... } | null
}
```

### Endpoints

#### 1. Create User

**POST** `/api/users`

Creates a new user account.

**Request Body:**
```json
{
  "name": "Blessed Milt",
  "email": "blessed@example.com"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "User created successfully",
  "data": {
    "id": 1,
    "name": "Blessed Milt",
    "email": "blessed@example.com",
    "created_at": "2026-02-25T10:00:00.000000Z",
    "updated_at": "2026-02-25T10:00:00.000000Z"
  }
}
```

**Validation Errors (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "data": {
    "errors": {
      "email": ["The email has already been taken."]
    }
  }
}
```

---

#### 2. Get User Details

**GET** `/api/users/{id}`

Retrieves user information with all wallets and total balance across all wallets.

**Success Response (200):**
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Blessed Milt",
      "email": "blessed@example.com"
    },
    "wallets": [
      {
        "id": 1,
        "name": "Business A Wallet",
        "balance": "1500.00"
      },
      {
        "id": 2,
        "name": "Personal Savings",
        "balance": "5000.00"
      }
    ],
    "total_balance": "6500.00"
  }
}
```

**Not Found (404):**
```json
{
  "success": false,
  "message": "User not found",
  "data": null
}
```

---

#### 3. Create Wallet

**POST** `/api/wallets`

Creates a new wallet for a user.

**Request Body:**
```json
{
  "user_id": 1,
  "name": "Business A Wallet"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Wallet created successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Business A Wallet",
    "balance": "0.00",
    "created_at": "2026-02-25T10:00:00.000000Z",
    "updated_at": "2026-02-25T10:00:00.000000Z"
  }
}
```

---

#### 4. Get Wallet Details

**GET** `/api/wallets/{id}`

Retrieves wallet information with transaction history and current balance.

**Success Response (200):**
```json
{
  "success": true,
  "message": "Wallet retrieved successfully",
  "data": {
    "wallet": {
      "id": 1,
      "user_id": 1,
      "name": "Business A Wallet",
      "created_at": "2026-02-25T10:00:00.000000Z",
      "updated_at": "2026-02-25T10:00:00.000000Z"
    },
    "balance": "1500.00",
    "transactions": [
      {
        "id": 1,
        "wallet_id": 1,
        "type": "income",
        "amount": "2000.00",
        "created_at": "2026-02-25T10:30:00.000000Z",
        "updated_at": "2026-02-25T10:30:00.000000Z"
      },
      {
        "id": 2,
        "wallet_id": 1,
        "type": "expense",
        "amount": "500.00",
        "created_at": "2026-02-25T11:00:00.000000Z",
        "updated_at": "2026-02-25T11:00:00.000000Z"
      }
    ]
  }
}
```

---

#### 5. Create Transaction

**POST** `/api/transactions`

Creates a new income or expense transaction. Automatically updates wallet balance.

**Request Body:**
```json
{
  "wallet_id": 1,
  "type": "income",
  "amount": 500.00
}
```

**Type values:**
- `income` - Adds to wallet balance
- `expense` - Subtracts from wallet balance

**Success Response (201):**
```json
{
  "success": true,
  "message": "Transaction created successfully",
  "data": {
    "transaction": {
      "id": 1,
      "wallet_id": 1,
      "type": "income",
      "amount": "500.00",
      "created_at": "2026-02-25T10:00:00.000000Z",
      "updated_at": "2026-02-25T10:00:00.000000Z"
    },
    "wallet_balance": "500.00"
  }
}
```

**Insufficient Funds (422):**
```json
{
  "success": false,
  "message": "Insufficient funds",
  "data": null
}
```

---

## Database Schema

### Users Table
```
id              BIGINT (Primary Key)
name            VARCHAR(255)
email           VARCHAR(255) UNIQUE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Wallets Table
```
id              BIGINT (Primary Key)
user_id         BIGINT (Foreign Key -> users.id, CASCADE DELETE)
name            VARCHAR(255)
balance         DECIMAL(15,2) DEFAULT 0.00
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Transactions Table
```
id              BIGINT (Primary Key)
wallet_id       BIGINT (Foreign Key -> wallets.id, CASCADE DELETE)
type            ENUM('income', 'expense')
amount          DECIMAL(15,2)
description     TEXT (NULLABLE)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Relationships
- **User** has many **Wallets** (1:N)
- **Wallet** belongs to **User** (N:1)
- **Wallet** has many **Transactions** (1:N)
- **Transaction** belongs to **Wallet** (N:1)

## Testing

### Run All Tests

```bash
php artisan test
```

Or using Composer:

```bash
composer run test
```

### Test Structure

Tests are located in the `tests/` directory:
- `tests/Feature/` - Integration tests for API endpoints
- `tests/Unit/` - Unit tests for individual classes

## Development

### Code Quality

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Common Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View routes
php artisan route:list

# Database operations
php artisan migrate:fresh     # Drop all tables and re-migrate
php artisan migrate:rollback  # Rollback last migration

# Tinker (REPL)
php artisan tinker
```

### Project Structure

```
app/
├── Http/
│   └── Controllers/          # API Controllers
│       ├── UserController.php
│       ├── WalletController.php
│       └── TransactionController.php
├── Models/                   # Eloquent Models
│   ├── User.php
│   ├── Wallet.php
│   └── Transaction.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/               # Database migrations
└── seeders/                  # Database seeders

routes/
├── api.php                   # API routes
└── web.php                   # Web routes

tests/
├── Feature/                  # Feature tests
└── Unit/                     # Unit tests
```

## Security Features

1. **Email Validation**: Prevents duplicate user accounts
2. **Database Constraints**: Foreign keys with cascade delete
3. **Transaction Locking**: Row-level locking prevents race conditions
4. **Input Validation**: All inputs are validated before processing
5. **Insufficient Funds Check**: Prevents overdrafts on expense transactions
6. **Database Transactions**: Ensures data consistency with atomic operations

## Troubleshooting

### Database Issues

If you encounter database errors:

```bash
# Reset the database
php artisan migrate:fresh

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Permission Issues (Unix/Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear All Caches

```bash
php artisan optimize:clear
```

## API Usage Examples

### Using cURL

```bash
# Create a user
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"Blessed Milt","email":"blessed@example.com"}'

# Create a wallet
curl -X POST http://localhost:8000/api/wallets \
  -H "Content-Type: application/json" \
  -d '{"user_id":1,"name":"My Wallet"}'

# Add income transaction
curl -X POST http://localhost:8000/api/transactions \
  -H "Content-Type: application/json" \
  -d '{"wallet_id":1,"type":"income","amount":1000.00}'

# Get user details
curl http://localhost:8000/api/users/1
```

## Future Enhancements

Potential features for future development:
- User authentication (Laravel Sanctum/Passport)
- Transaction categories and tags
- Date range filtering for transactions
- Transaction updates and deletions
- Wallet sharing between users
- Budget management
- Recurring transactions
- Export functionality (CSV, PDF)
- Analytics and reporting

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
