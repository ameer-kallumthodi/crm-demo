# Basic CRM - Laravel 12

A comprehensive Customer Relationship Management system built with Laravel 12, designed for educational institutions and lead management.

## Features

- **User Management**: Complete user authentication and role-based access control
- **Lead Management**: Full CRUD operations for leads with filtering and search
- **Dashboard**: Comprehensive dashboard with statistics and analytics
- **Team Management**: Team and telecaller management
- **Lead Status & Sources**: Configurable lead statuses and sources
- **Country & Course Management**: Support for multiple countries and courses
- **Notification System**: Real-time notifications with auto-mark as read functionality
- **Call Log Integration**: Voxbay integration for call logging and management
- **Bulk Operations**: Bulk lead operations (reassign, delete, convert)
- **Reporting System**: Comprehensive reports with Excel and PDF export
- **Role-Based Access**: Granular permissions for different user roles
- **Responsive Design**: Mobile-friendly interface with modern UI/UX

## Quick Start

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ameer-kallumthodi/crm-demo.git
   cd crm-demo
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

4. **Database setup**:
   - Create a MySQL database named `crm_demo`
   - Update `.env` file with your database credentials
   - Run migrations: `php artisan migrate --seed`

5. **Start the server**:
   ```bash
   php artisan serve
   ```

6. **Access the application**:
   - Open `http://localhost:8000`
   - Login with: `superadmin@crm.com` / `password`

## Technology Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Frontend**: Bootstrap 5, jQuery
- **UI Components**: Tabler Icons
- **Export**: Maatwebsite Excel, DomPDF

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, email support@crm.com or create an issue in the GitHub repository.
