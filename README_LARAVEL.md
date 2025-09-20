# Basic CRM - Laravel 12

This is the Laravel 12 version of the Skillage CRM system, migrated from CodeIgniter 4.

## Features

### Core Features
- **User Management**: Complete user authentication and role-based access control
- **Lead Management**: Full CRUD operations for leads with filtering and search
- **Dashboard**: Comprehensive dashboard with statistics and analytics
- **Team Management**: Team and telecaller management
- **Lead Status & Sources**: Configurable lead statuses and sources
- **Country & Course Management**: Support for multiple countries and courses

### Advanced Features
- **Notification System**: Real-time notifications with auto-mark as read functionality
- **Call Log Integration**: Voxbay integration for call logging and management
- **Bulk Operations**: Bulk lead operations (reassign, delete, convert)
- **Reporting System**: Comprehensive reports with Excel and PDF export
- **Role-Based Access**: Granular permissions for different user roles
- **Responsive Design**: Mobile-friendly interface with modern UI/UX
- **Data Export**: Export leads and reports in multiple formats
- **Search & Filtering**: Advanced search and filtering capabilities

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ameer-kallumthodi/crm-demo.git
   cd crm-demo
   ```

2. **Git setup** (if you want to contribute):
   ```bash
   cd D:\trogon-projects\skillage_crm\skillage_crm_laravel
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

4. **Environment setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**:
   - Update `.env` file with your database credentials
   - Database name: `crm_laravel`
   - Username: `trogon_skillage_crm`
   - Password: `r%puaEc!jcLk`

6. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Start the development server**:
   ```bash
   php artisan serve
   ```

8. **Access the application**:
   - Open your browser and go to `http://localhost:8000`
   - Use the default credentials to login

## Default Login Credentials

- **Email**: superadmin@crm.com
- **Password**: password

## Project Structure

### Models
- `User` - User management with roles and teams
- `Lead` - Lead management with relationships
- `UserRole` - User role definitions
- `Team` - Team management
- `LeadStatus` - Lead status definitions
- `LeadSource` - Lead source definitions
- `Country` - Country management
- `Course` - Course management
- `Notification` - Notification system
- `NotificationRead` - Notification read tracking
- `VoxbayCallLog` - Call log management
- `ConvertedLead` - Converted leads tracking

### Controllers
- `AuthController` - Authentication (login/logout)
- `DashboardController` - Dashboard with statistics
- `LeadController` - Lead CRUD operations
- `NotificationController` - Notification management
- `VoxbayController` - Call integration
- `VoxbayCallLogController` - Call log management
- `LeadReportController` - Reporting system
- `TeamController` - Team management
- `TelecallerController` - Telecaller management

### Views
- `auth/login.blade.php` - Login page
- `dashboard.blade.php` - Main dashboard
- `leads/` - Lead management views
- `admin/notifications/` - Notification management
- `notifications/` - User notification views
- `admin/reports/` - Reporting views
- `layouts/` - Layout templates with topbar notifications

### Helpers
- `AuthHelper` - Authentication helper functions
- `RoleHelper` - Role management helper
- `PermissionHelper` - Permission management
- `StatusHelper` - Status management
- `PhoneNumberHelper` - Phone number formatting

## Database Schema

The database includes the following main tables:
- `users` - User accounts with roles and team assignments
- `user_roles` - Role definitions
- `teams` - Team management
- `leads` - Lead information
- `lead_statuses` - Lead status definitions
- `lead_sources` - Lead source definitions
- `countries` - Country information
- `courses` - Course information
- `notifications` - Notification system
- `notification_reads` - Notification read tracking
- `voxbay_call_logs` - Call log management
- `converted_leads` - Converted leads tracking
- `settings` - System settings

## Key Features Migrated from CI4

1. **Authentication System**: Complete login/logout with session management
2. **Lead Management**: Full lead CRUD with filtering and search
3. **User Roles**: Role-based access control (Admin, Manager, Telecaller, etc.)
4. **Team Management**: Team and telecaller assignment
5. **Dashboard Analytics**: Lead statistics and reporting
6. **File Upload**: Support for profile pictures and documents

## API Endpoints

- `GET /` - Login page
- `POST /login` - Login authentication
- `POST /logout` - Logout
- `GET /dashboard` - Dashboard
- `GET /leads` - List leads
- `POST /leads` - Create lead
- `GET /leads/{id}` - Show lead
- `PUT /leads/{id}` - Update lead
- `DELETE /leads/{id}` - Delete lead

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

## Support

For any issues or questions, please refer to the Laravel documentation or contact the development team.
