# Shiloh Attendance and Payment Monitoring System

## Phase 0 - Project Setup Complete ✅

A Laravel 11 + Filament v3 application for managing attendance and payments at Shiloh's Learning And Development Center.

## Tech Stack

- **Framework**: Laravel 11
- **Admin Panel**: Filament v3 (responsive)
- **Database**: PostgreSQL (Supabase)
- **Authentication**: Laravel built-in (not Supabase Auth)
- **Roles**: Simple enum-based (ADMIN, USER)

## Features Implemented (Phase 0)

✅ Laravel 11 project scaffold
✅ Filament v3 admin panel with login
✅ Role-based access control (ADMIN/USER)
✅ User management (CRUD)
✅ Default admin user seeded
✅ Responsive dashboard for both roles
✅ Security: password hashing, validation, policies
✅ PostgreSQL configuration for Supabase

## Installation

### 1. Clone and Install Dependencies

```bash
composer install
```

### 2. Configure Environment

Copy `.env.example` to `.env` and update with your Supabase credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=your-supabase-host.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your-password
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Admin User

```bash
php artisan db:seed
```

### 6. Start Development Server

```bash
php artisan serve
```

## Access

- **Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Default Admin**:
  - Email: `admin@shiloh.local`
  - Password: `Admin123!`

## User Roles

### ADMIN
- Full access to all features
- User management (create, edit, delete users)
- View admin dashboard with statistics
- Access to all navigation items

### USER
- Limited access to personal features
- View personal dashboard
- Access to attendance pages (future phases)
- Cannot access admin features

## Project Structure

```
app/
├── Enums/
│   └── UserRole.php              # Role enum (ADMIN, USER)
├── Filament/
│   ├── Pages/
│   │   └── Dashboard.php         # Custom dashboard
│   └── Resources/
│       └── UserResource.php      # User CRUD
├── Models/
│   └── User.php                  # User model with role
├── Policies/
│   └── UserPolicy.php            # Authorization policies
└── Providers/
    ├── AppServiceProvider.php    # App configuration
    ├── AuthServiceProvider.php   # Policy registration
    └── Filament/
        └── AdminPanelProvider.php # Filament config

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   └── 0001_01_01_000002_create_jobs_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── AdminSeeder.php           # Seeds admin user

resources/
└── views/
    └── filament/
        └── pages/
            └── dashboard.blade.php # Dashboard view
```

## Security Features

- ✅ Password hashing (bcrypt)
- ✅ CSRF protection
- ✅ Input validation
- ✅ Policy-based authorization
- ✅ Role-based access control
- ✅ Session management
- ✅ Secure password reset

## Next Phases

- **Phase 1**: Student/Staff management
- **Phase 2**: Attendance tracking
- **Phase 3**: Payment monitoring
- **Phase 4**: Reports and analytics

## Development

### Create New User

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
    'role' => \App\Enums\UserRole::USER,
]);
```

### Run Tests

```bash
php artisan test
```

## License

Proprietary - Shiloh's Learning And Development Center
