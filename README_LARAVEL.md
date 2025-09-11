# Skillage CRM - Laravel 12

This is the Laravel 12 version of the Skillage CRM system, migrated from CodeIgniter 4.

## Features

- **User Management**: Complete user authentication and role-based access control
- **Lead Management**: Full CRUD operations for leads with filtering and search
- **Dashboard**: Comprehensive dashboard with statistics and analytics
- **Team Management**: Team and telecaller management
- **Lead Status & Sources**: Configurable lead statuses and sources
- **Country & Course Management**: Support for multiple countries and courses

## Installation

1. **Clone the repository** (if not already done):
   ```bash
   cd D:\trogon-projects\skillage_crm\skillage_crm_laravel
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Environment setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**:
   - Update `.env` file with your database credentials
   - Database name: `crm_laravel`
   - Username: `trogon_skillage_crm`
   - Password: `r%puaEc!jcLk`

5. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**:
   ```bash
   php artisan serve
   ```

## Default Login Credentials

- **Email**: admin@skillagecrm.com
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

### Controllers
- `AuthController` - Authentication (login/logout)
- `DashboardController` - Dashboard with statistics
- `LeadController` - Lead CRUD operations

### Views
- `auth/login.blade.php` - Login page
- `dashboard.blade.php` - Main dashboard
- `leads/` - Lead management views

### Helpers
- `AuthHelper` - Authentication helper functions

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

## Migration from CodeIgniter 4

This Laravel project includes all the core functionality from the original CodeIgniter 4 project:

- ✅ User authentication and session management
- ✅ Lead management with full CRUD operations
- ✅ Role-based access control
- ✅ Team and telecaller management
- ✅ Dashboard with analytics
- ✅ Database migrations and seeders
- ✅ Helper functions for authentication
- ✅ Responsive UI with Bootstrap 5

## Support

For any issues or questions, please refer to the Laravel documentation or contact the development team.
