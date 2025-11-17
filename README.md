# Laravel SaaS Multi-Tenancy Boilerplate

A complete Laravel boilerplate for building SaaS applications with multi-tenancy support. This boilerplate includes a full onboarding flow for creating companies with custom subdomains, isolated tenant databases, and tenant-aware routing.

## Features

- **Multi-Tenancy Architecture**: Each tenant gets their own isolated database
- **Subdomain-Based Routing**: Automatic tenant identification via subdomains
- **Onboarding Flow**: Complete company registration with subdomain validation
- **Real-time Subdomain Validation**: AJAX-based subdomain availability checking
- **Tenant Isolation**: Database, cache, filesystem, and queue isolation per tenant
- **RESTful API**: Full CRUD API endpoints for tenant management
- **Modern UI**: TailwindCSS-powered responsive interface
- **Production Ready**: Built on Laravel 12 with best practices

## Tech Stack

- **Laravel 12.x**: Modern PHP framework
- **Tenancy for Laravel (stancl/tenancy)**: Multi-tenancy package
- **MySQL**: Primary database (configurable)
- **TailwindCSS**: Utility-first CSS framework

## Quick Start

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with your database credentials
# DB_DATABASE=saas_central
# APP_DOMAIN=localhost

# Run migrations
php artisan migrate

# Start server
php artisan serve

# Visit http://localhost:8000/onboard to create your first tenant
```

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js & NPM (optional, for frontend assets)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd legendary-engine
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**

   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=saas_central
   DB_USERNAME=root
   DB_PASSWORD=your_password

   APP_URL=http://localhost
   APP_DOMAIN=localhost
   ```

5. **Create the central database**
   ```bash
   mysql -u root -p
   CREATE DATABASE saas_central;
   exit
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Configure your hosts file** (for local development)

   Add to `/etc/hosts` (Linux/Mac) or `C:\Windows\System32\drivers\etc\hosts` (Windows):
   ```
   127.0.0.1 localhost
   127.0.0.1 tenant1.localhost
   127.0.0.1 tenant2.localhost
   ```

   **Note**: Chrome supports `.localhost` wildcard DNS natively, so subdomains will work automatically

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - Central app: `http://localhost:8000`
   - Onboarding: `http://localhost:8000/onboard`

## Architecture

### Multi-Tenancy Structure

```
Central Database (saas_central)
├── tenants table (stores tenant information)
├── domains table (stores tenant domains/subdomains)
└── migrations table

Tenant Database (tenant{uuid})
├── users table
├── posts table (example)
└── other tenant-specific tables
```

### Key Components

- **OnboardingController**: Handles company registration and subdomain validation
- **TenantController**: API endpoints for tenant management
- **TenancyServiceProvider**: Configures multi-tenancy events and routing
- **InitializeTenancyBySubdomain**: Middleware for subdomain-based tenant identification

## Usage

### Creating a New Company/Tenant

1. Navigate to `http://localhost:8000/onboard`
2. Fill in the company registration form:
   - Company Name
   - Subdomain (validated in real-time)
   - Admin Email
   - Password
3. Submit the form
4. Access your tenant at `http://{subdomain}.localhost:8000`

### Subdomain Validation

The onboarding form includes real-time subdomain validation:
- Automatically converts to lowercase
- Validates format (alphanumeric and hyphens only)
- Checks availability via AJAX
- Provides immediate feedback to users

### API Endpoints

All API endpoints are available at `/api/tenants`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tenants` | List all tenants |
| POST | `/api/tenants` | Create a new tenant |
| GET | `/api/tenants/{id}` | Get tenant details |
| PUT | `/api/tenants/{id}` | Update tenant |
| DELETE | `/api/tenants/{id}` | Delete tenant |

#### Example: Create a Tenant

```bash
POST /api/tenants
Content-Type: application/json

{
  "company_name": "Acme Corporation",
  "subdomain": "acme"
}
```

## Development

### Adding Tenant Migrations

Tenant-specific migrations go in `database/migrations/tenant/`:

```bash
php artisan make:migration create_posts_table
# Move to database/migrations/tenant/
```

Run tenant migrations:
```bash
php artisan tenants:migrate
```

### Adding Tenant Routes

Add routes in `routes/tenant.php`:

```php
Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
});
```

### Accessing Tenant Data

```php
// Automatically scoped to current tenant
$posts = Post::all();
$tenant = tenant(); // Get current tenant
$tenantId = tenant('id');
$companyName = tenant('company_name');
```

## Configuration

### Central Domains

Edit `config/tenancy.php`:

```php
'central_domains' => [
    '127.0.0.1',
    'localhost',
    'yourdomain.com', // Production domain
],
```

### Database Configuration

```php
'database' => [
    'prefix' => 'tenant',
    'suffix' => '',
],
```

Creates databases like: `tenant{uuid}`

## Production Deployment

### 1. Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_DOMAIN=yourdomain.com
```

### 2. Wildcard DNS

Configure wildcard DNS:
```
A     @              your-server-ip
A     *              your-server-ip
```

### 3. SSL Certificate

Use a wildcard SSL certificate: `*.yourdomain.com`

### 4. Queue Configuration

For production, use a queue driver and run workers:

```bash
php artisan queue:work
```

Enable queuing in `TenancyServiceProvider.php`:
```php
])->shouldBeQueued(true),
```

## Security Features

- Database isolation per tenant
- Subdomain validation and sanitization
- CSRF protection
- Password hashing (bcrypt)
- Automatic tenant context scoping

## Troubleshooting

### Subdomain not working locally

1. Chrome supports `.localhost` wildcard natively
2. For other browsers, update hosts file
3. Try incognito mode to clear DNS cache

### Database connection errors

1. Verify central database exists
2. Check MySQL credentials in `.env`
3. Grant database creation privileges:
   ```sql
   GRANT ALL PRIVILEGES ON *.* TO 'your_user'@'localhost';
   ```

### Tenant database not created

1. Check `storage/logs/laravel.log`
2. Verify database manager in `config/tenancy.php`
3. Ensure proper database permissions

## Directory Structure

```
app/Http/Controllers/
├── OnboardingController.php      # Company onboarding
└── Api/TenantController.php      # Tenant API

resources/views/
├── onboarding/                    # Onboarding views
│   ├── create.blade.php
│   └── success.blade.php
├── tenant/                        # Tenant dashboard
│   └── dashboard.blade.php
└── layouts/app.blade.php          # Main layout

routes/
├── web.php                        # Central routes
└── tenant.php                     # Tenant routes

database/migrations/
├── *_create_tenants_table.php
├── *_create_domains_table.php
└── tenant/                        # Tenant migrations
```

## Extending the Boilerplate

### Add Authentication

```bash
composer require laravel/breeze --dev
php artisan breeze:install
```

### Add Permissions

```bash
composer require spatie/laravel-permission
```

### Add Billing

```bash
composer require laravel/cashier
```

## Roadmap

- [ ] Authentication system integration
- [ ] Team/user management within tenants
- [ ] Billing and subscription management
- [ ] Admin dashboard for managing all tenants
- [ ] Tenant analytics and usage tracking
- [ ] Email verification
- [ ] Two-factor authentication
- [ ] API rate limiting
- [ ] Tenant resource limits/quotas
- [ ] Backup/restore functionality
- [ ] Tenant impersonation (admin feature)

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Tenancy for Laravel](https://tenancyforlaravel.com)
- [TailwindCSS](https://tailwindcss.com)

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

**Ready to build your SaaS?** Visit `http://localhost:8000/onboard` to create your first tenant!
