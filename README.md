````markdown
# Laravel Sanctum Auth API Project

This is a Laravel-based REST API using Sanctum for authentication. The system includes user registration, login, token-based auth, and a scheduled weekly report command with Mailtrap integration.

## 🔧 Tech Stack

- PHP 7.3+
- Laravel 10
- Sanctum
- MySQL
- Mailtrap (for email testing)
- Postman (for API testing)

---

## 🚀 Getting Started

### 1. Clone the Project

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
````

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

Copy the example `.env` file and modify as needed:

```bash
cp .env.example .env
```

Update `.env`:

```env
APP_NAME=LaravelSanctumAPI
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@example.com
MAIL_FROM_NAME="Sanctum API"
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Serve the Application

```bash
php artisan serve
```

---

## ✅ API Testing with Postman

Import the `postman_collection.json` (if available) or use the following endpoints manually:

### 🔐 Auth Routes

| Method | URL           | Description                    |
| ------ | ------------- | ------------------------------ |
| POST   | /api/register | Register a user                |
| POST   | /api/login    | Login a user                   |
| GET    | /api/user     | Get user details *(protected)* |
| POST   | /api/logout   | Logout *(protected)*           |

**Headers for protected routes:**

```http
Authorization: Bearer <token>
Accept: application/json
```

---

## 📬 Email Sending

We use Mailtrap for local testing.

A scheduled command will send weekly reports:

```bash
php artisan report:weekly-waiting-list
```

### If you want to test it manually:

```bash
php artisan tinker
```

```php
$user = App\Models\User::first();
Mail::to($user->email)->send(new App\Mail\WeeklyReportMail($user));
```

---

## 🧠 Project Logic & Decisions

* **Sanctum** is chosen for simple token-based API authentication.
* Custom `SendWeeklyReport` command was built to demonstrate Laravel's scheduler and email logic.
* Emails are handled via Mailtrap for safe, local testing.
* Clean code architecture and proper naming conventions are followed.
* Error handling and validation are in place for all API endpoints.

---
