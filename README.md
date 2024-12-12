# Expense Tracker Application

A Laravel-based expense tracking application with user authentication, expense management, and category organization.

## Features

- User Authentication (Login, Register, Password Reset)
- Expense Management (CRUD operations)
- Category Management
- Dashboard with Monthly Overview
- Password Reset via Email

## Requirements

- PHP >= 8.1
- Composer
- MySQL
- Node.js & NPM

## Installation

1. Clone the repository
```bash
git clone <repository-url>
cd expense-tracker
```

2. Install PHP dependencies
```bash
composer install
```

3. Install NPM dependencies
```bash
npm install
```

4. Copy .env.example to .env
```bash
cp .env.example .env
```

5. Configure your .env file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_tracker
DB_USERNAME=root
DB_PASSWORD="Admin123#"
```

6. Generate application key
```bash
php artisan key:generate
```

7. Run database migrations
```bash
php artisan migrate
```

8. Build assets
```bash
npm run build
```

## Email Configuration (Password Reset)

To enable password reset functionality, configure your SMTP settings in the .env file:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@expense-tracker.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For testing, you can use [Mailtrap](https://mailtrap.io/) - sign up for a free account and get the credentials.

## Running the Application

1. Start the development server
```bash
php artisan serve
```

2. Access the application at http://localhost:8000

## Usage

1. Register a new account
2. Create expense categories
3. Add expenses
4. View your expense dashboard
5. Use the "Forgot Password" link on the login page if you need to reset your password

## Security

- CSRF Protection
- Password Hashing
- Authentication
- Authorization Policies
