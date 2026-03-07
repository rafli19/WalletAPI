# 💰 WalletAPI — Backend

REST API for WalletApp Digital Wallet Management System, built with **Laravel 11** and **Laravel Sanctum**.

## ✨ Features

- Authentication with Laravel Sanctum (Bearer Token)
- Top Up balance with admin approval flow
- Transfer balance between users
- Transaction & mutation history
- Role-based access control (User & Admin)
- Admin: manage users (CRUD), approve/reject top ups
- Avatar upload support

---

## 🧱 Tech Stack

| | |
|---|---|
| Framework | Laravel 11 |
| Auth | Laravel Sanctum |
| Database | MySQL |
| Storage | Local (public disk) |

---

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/WalletAPI.git
cd WalletAPI

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wallet_app
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Create storage symlink (for avatar uploads)
php artisan storage:link

# 8. Start development server
php artisan serve
```

API will run at `http://localhost:8000`

---

## 📡 API Endpoints

### Auth
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login |
| POST | `/api/logout` | Logout |
| GET | `/api/me` | Get current user |

### User
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/topup` | Request top up |
| POST | `/api/transfer` | Transfer balance |
| GET | `/api/transactions` | Get transaction history |
| PUT | `/api/profile` | Update profile |

### Admin
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/topups` | Get all top up requests |
| POST | `/api/admin/topups/{id}/approve` | Approve top up |
| POST | `/api/admin/topups/{id}/reject` | Reject top up |
| GET | `/api/admin/users` | Get all users |
| POST | `/api/admin/users` | Create user |
| PUT | `/api/admin/users/{id}` | Update user |
| DELETE | `/api/admin/users/{id}` | Delete user |

---

## 👥 User Roles

| Role | Access |
|------|--------|
| **User** | Top up, transfer, view transactions, edit profile |
| **Admin** | Approve/reject top ups, manage all users |

> To create an admin account, run:
> ```bash
> php artisan tinker --execute="App\Models\User::where('email', 'admin@mail.com')->update(['role' => 'admin']);"
> ```

---

## 📁 Project Structure

```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php
│   └── WalletController.php
└── Models/
    ├── User.php
    └── Transaction.php
routes/
└── api.php
database/
└── migrations/
```

---

## 🌐 Production Deployment

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-api-domain.com

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📄 License

Built for educational purposes — Dibimbing.id FWD Project.
