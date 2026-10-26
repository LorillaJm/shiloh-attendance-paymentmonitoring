# Shiloh Attendance and Payment Monitoring System

A comprehensive web-based management system for educational institutions, designed to streamline student enrollment, attendance tracking, payment monitoring, and parent-teacher communication.

## Overview

This system provides an all-in-one solution for managing learning center operations, including real-time attendance tracking, automated payment scheduling with installment support, session management with teacher assignments, and a dedicated parent portal for monitoring student progress.

## Key Features

### 📊 Student Management
- Complete student profile management with demographics and guardian information
- Automatic student number generation (SHILOH-YYYY-XXXX format)
- Student status tracking (Active, Inactive, Dropped)
- Multi-guardian support per student

### 💳 Payment & Enrollment System
- Flexible package-based enrollment with customizable pricing
- Automated installment scheduling with configurable down payment percentages
- Payment tracking with multiple payment methods (Cash, Bank Transfer, GCash, etc.)
- Real-time payment status monitoring (Paid, Unpaid, Overdue)
- Comprehensive student ledger with payment history
- Excel/PDF export for collection reports and student ledgers

### 📅 Attendance & Session Management
- Daily attendance encoding with bulk operations
- Multiple session types support (One-on-One, Group Play, Writing, Practice Speech)
- Recurring schedule management (Daily, Weekly, Custom patterns)
- Teacher assignment and session occurrence tracking
- Special monitoring notes for students age 10 and below
- Attendance reports with filtering by date range and student

### 👨‍👩‍👧‍👦 Parent Portal
- Secure login for guardians to monitor their children
- Real-time view of student attendance records
- Payment status and transaction history
- Upcoming session schedules
- Receive system announcements and notifications
- Multi-child support for guardians with multiple students

### 📈 Reporting & Analytics
- Interactive dashboard with key metrics and statistics
- Daily attendance reports with export capabilities
- Monthly attendance summaries by program
- Payment collection reports with date range filtering
- Due and overdue payment tracking
- Student ledger generation (PDF)
- Attendance sheet reports for administrative use

### 🔔 Real-time Notifications
- Web push notifications for payment reminders
- Announcement broadcasting to parents and admins
- Laravel Reverb integration for WebSocket-based real-time updates
- Email notification support for important events

### 🔐 Access Control
- Role-based access (Superadmin, Admin, Parent)
- Secure authentication with email verification
- Profile management with theme preferences
- Session management and security controls

## Technology Stack

- **Backend Framework**: Laravel 11.x (PHP 8.2+)
- **Admin Panel**: Filament 3.2
- **Database**: PostgreSQL 14+ (Supabase supported)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Real-time**: Laravel Reverb (WebSockets)
- **Notifications**: Laravel WebPush, Firebase Cloud Messaging
- **PDF Generation**: DomPDF
- **Excel Export**: Maatwebsite Excel
- **Package Manager**: Composer, npm

## System Requirements

- PHP 8.2 or higher
- PostgreSQL 14 or higher
- Node.js 18.x or higher
- Composer 2.x
- Web server (Apache/Nginx)

### Required PHP Extensions
```
php-pgsql, php-mbstring, php-xml, php-curl
php-zip, php-gd, php-intl, php-bcmath
```

## Installation

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url> shiloh-attendance
   cd shiloh-attendance
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update database credentials in `.env`**
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-database-host
   DB_PORT=5432
   DB_DATABASE=your-database-name
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```

5. **Setup database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

8. **Access the system**
   - Admin: http://localhost:8000/admin/login
   - Parent Portal: http://localhost:8000/parent/login

### Default Credentials

**Superadmin Account:**
- Email: `superadmin@shiloh.edu`
- Password: `changeme`

**Admin Account:**
- Email: `admin@shiloh.local`
- Password: `Admin123!`

⚠️ **Change default passwords immediately after first login!**

## Documentation

- **[Setup Guide](SETUP.md)** - Detailed installation and configuration instructions
- **[Data Dictionary](DATA_DICTIONARY.md)** - Complete database schema documentation

## Key Modules

### Admin Panel Features
- Dashboard with real-time statistics and charts
- Student enrollment and profile management
- Payment processing and schedule management
- Attendance encoding with bulk operations
- Session schedule configuration
- Parent account management
- System announcements
- Comprehensive reporting tools
- Database backup management

### Parent Portal Features
- Children's attendance history
- Payment status and transaction records
- Upcoming session schedules
- Receive announcements
- Profile management
- Multi-child monitoring

## Security Features

- Secure authentication with bcrypt password hashing
- Role-based access control (RBAC)
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection with Blade templating
- Rate limiting on authentication attempts
- Secure session management
- SSL/TLS support for database connections

## Performance Optimizations

- Database query optimization with eager loading
- Redis caching support for frequently accessed data
- Route and config caching for production
- Asset minification and bundling
- Lazy loading for large data sets
- Indexed database columns for faster queries

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## License

This project is licensed under the MIT License.

## Support

For setup assistance or technical issues, refer to the [Setup Guide](SETUP.md) or check the troubleshooting section.

---

**Version:** 1.0  
**Last Updated:** October 2026  
**Developed for:** Shiloh's Learning and Development Center
