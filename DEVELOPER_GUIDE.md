# School Management System - Developer Guide

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Project Setup](#project-setup)
3. [Database Design](#database-design)
4. [Authentication & Authorization](#authentication--authorization)
5. [File Structure](#file-structure)
6. [Development Workflow](#development-workflow)
7. [Testing Guidelines](#testing-guidelines)
8. [Deployment Guide](#deployment-guide)
9. [Performance Optimization](#performance-optimization)
10. [Security Considerations](#security-considerations)
11. [API Best Practices](#api-best-practices)
12. [Troubleshooting](#troubleshooting)

---

## System Architecture

### Overview

The School Management System is built using Laravel 11 with a clean architecture approach:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend/     │    │   API Layer     │    │   Business      │
│   Mobile App    │◄───┤   (Controllers) │◄───┤   Logic Layer   │
│                 │    │                 │    │   (Services)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │                       │
                                ▼                       ▼
                       ┌─────────────────┐    ┌─────────────────┐
                       │   Data Layer    │    │   External      │
                       │   (Models)      │    │   Services      │
                       │                 │    │   (Mail, SMS)   │
                       └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │   Database      │
                       │   (MySQL)       │
                       └─────────────────┘
```

### Core Principles

1. **Separation of Concerns**: Controllers handle HTTP requests, Services contain business logic, Models handle data access.
2. **Single Responsibility**: Each class has one clear purpose.
3. **Dependency Injection**: Services are injected into controllers for better testability.
4. **Resource-Based API**: Consistent API structure using Laravel resources.
5. **Permission-Based Security**: Fine-grained access control using Spatie Laravel Permission.

### Technology Stack

- **Backend Framework**: Laravel 11
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum (JWT)
- **Authorization**: Spatie Laravel Permission
- **File Storage**: Laravel Storage (configurable)
- **API Documentation**: L5 Swagger
- **Testing**: PHPUnit & Pest
- **Queue System**: Redis/Database
- **Caching**: Redis

---

## Project Setup

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+
- Redis (optional, for caching and queues)

### Installation Steps

1. **Clone the Repository**
```bash
git clone <repository-url>
cd school-management-system
```

2. **Install PHP Dependencies**
```bash
composer install
```

3. **Install Node Dependencies**
```bash
npm install
```

4. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure Environment Variables**
```env
# Application
APP_NAME="School Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Configuration
JWT_SECRET=your_jwt_secret_key

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls

# File Storage
FILESYSTEM_DISK=public

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

6. **Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Create storage link
php artisan storage:link
```

7. **Generate API Documentation**
```bash
php artisan l5-swagger:generate
```

8. **Start Development Server**
```bash
php artisan serve
```

### Additional Setup

#### Queue Worker (for production)
```bash
php artisan queue:work --daemon
```

#### Schedule Cron Job
Add to crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Database Design

### Key Tables and Relationships

#### Core Tables

1. **users** - Central user table for all user types
2. **admins**, **teachers**, **students** - Role-specific information
3. **roles** and **permissions** - RBAC implementation
4. **model_has_roles** and **model_has_permissions** - Permission assignments

#### Academic Structure

```sql
years (Academic Years)
├── semesters
    ├── school_days
        ├── class_sessions
            ├── assignments
            ├── student_attendances
            └── teacher_attendances

grades
├── sections
    ├── student_enrollments
    ├── assignments
    └── subjects
        ├── teacher_section_subjects
        ├── assignments
        └── files
```

#### Content Management

```sql
news
├── news_targets (polymorphic)
└── file_downloads

files
├── file_targets (polymorphic)
└── file_downloads

assignments
├── assigned_session_id → class_sessions
├── due_session_id → class_sessions
├── subject_id → subjects
└── section_id → sections
```

### Key Relationships

#### User Polymorphism
```php
// User can be admin, teacher, or student
User::class
├── hasOne(Admin::class)
├── hasOne(Teacher::class)
└── hasOne(Student::class)
```

#### Target System (News & Files)
```php
// Polymorphic targeting system
NewsTarget::class
FileTarget::class
├── target_type (grade, section, student, teacher)
└── target_id (ID of the target)
```

#### Assignment Dependencies
```php
Assignment::class
├── belongsTo(ClassSession::class, 'assigned_session_id')
├── belongsTo(ClassSession::class, 'due_session_id')
├── belongsTo(Subject::class)
├── belongsTo(Section::class)
└── belongsTo(User::class, 'created_by')
```

### Database Conventions

1. **Table Names**: Snake case, plural (e.g., `student_attendances`)
2. **Foreign Keys**: Singular table name + `_id` (e.g., `user_id`)
3. **Pivot Tables**: Alphabetical order (e.g., `model_has_permissions`)
4. **Timestamps**: `created_at`, `updated_at`, `deleted_at` (for soft deletes)
5. **Polymorphic Columns**: `{relation}_type`, `{relation}_id`

---

## Authentication & Authorization

### Authentication Flow

1. **Login Request**: Client sends credentials with user type
2. **Validation**: System validates credentials against specific user type
3. **Token Generation**: JWT token created with user information
4. **Token Response**: Client receives token and user data
5. **Request Authentication**: Subsequent requests include Bearer token
6. **Token Validation**: Middleware validates token and loads user

### Authorization Levels

#### User Types
- **Admin**: Full system access
- **Teacher**: Academic content management, student data access
- **Student**: Limited read access to their data

#### Permission Categories
- **View Permissions**: Read access to data
- **Create Permissions**: Add new records
- **Update Permissions**: Modify existing records
- **Delete Permissions**: Soft delete records
- **Manage Deleted Permissions**: Restore/force delete

### Implementation Example

```php
// In Controller
public function store(StoreAssignmentRequest $request)
{
    return $this->assignmentService->createAssignment($request);
}

// In Request
public function authorize(): bool
{
    return auth()->user()->hasPermissionTo('انشاء واجب');
}

// In Service
public function createAssignment(Request $request): JsonResponse
{
    $this->checkPermission(PermissionEnum::CREATE_ASSIGNMENT);
    // Business logic...
}
```

---

## File Structure

### Application Structure

```
app/
├── Console/Commands/          # Artisan commands
├── Enums/                     # Enum classes
│   ├── PermissionEnum.php
│   ├── UserType.php
│   └── FileType.php
├── Exceptions/                # Custom exceptions
├── Helpers/                   # Helper classes
│   ├── AuthHelper.php
│   └── ResponseHelper.php
├── Http/
│   ├── Controllers/           # API controllers
│   ├── Requests/              # Form requests (validation)
│   ├── Resources/             # API resources
│   └── Kernel.php
├── Models/                    # Eloquent models
├── Services/                  # Business logic services
│   ├── News/                  # News management services
│   ├── Files/                 # File management services
│   └── [Other services]
├── Traits/                    # Reusable traits
└── View/Components/           # View components
```

### Service Organization

Services are organized by feature modules:

```
Services/
├── AuthService.php            # Authentication logic
├── UserService.php            # User management
├── AssignmentService.php      # Assignment operations
├── StudentAttendanceService.php
├── TeacherAttendanceService.php
├── News/                      # News module
│   ├── NewsService.php
│   ├── StoreNews.php
│   ├── UpdateNews.php
│   ├── ListNews.php
│   └── [Other news operations]
└── Files/                     # File module
    ├── FileService.php
    ├── StoreFile.php
    ├── DownloadFile.php
    └── [Other file operations]
```

### Route Organization

```
routes/
├── api.php                    # Main API routes
├── assignments.php            # Assignment routes
├── student-attendances.php    # Student attendance routes
├── teacher-attendances.php    # Teacher attendance routes
├── news.php                   # News management routes
├── files.php                  # File management routes
└── [Other feature routes]
```

---

## Development Workflow

### Coding Standards

1. **PSR-12 Compliance**: Follow PSR-12 coding standards
2. **Laravel Conventions**: Use Laravel naming conventions
3. **Type Hints**: Always use type hints for parameters and return types
4. **Documentation**: Document public methods with PHPDoc

### Development Process

1. **Feature Branch**: Create branch from `main`
```bash
git checkout -b feature/assignment-management
```

2. **Implementation**:
   - Create/update models
   - Create service classes
   - Create controllers
   - Create request classes
   - Create resources
   - Add routes
   - Write tests

3. **Testing**: Run tests before committing
```bash
php artisan test
# or
./vendor/bin/pest
```

4. **Code Review**: Create pull request for review

5. **Merge**: Merge to main after approval

### Code Example Template

```php
<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Helpers\ResponseHelper;
use App\Models\ModelName;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExampleService
{
    use HasPermissionChecks;

    /**
     * List items with pagination and filtering.
     */
    public function listItems(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_ITEMS);

        $query = ModelName::query();

        // Apply filters
        if ($request->has('filter_field')) {
            $query->where('field', $request->filter_field);
        }

        $items = $query->paginate(15);

        return ResponseHelper::jsonResponse(
            $items,
            'Items retrieved successfully'
        );
    }

    /**
     * Create a new item.
     */
    public function createItem(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_ITEM);

        $data = $request->validated();
        $item = ModelName::create($data);

        return ResponseHelper::jsonResponse(
            $item,
            'Item created successfully',
            201
        );
    }
}
```

---

## Testing Guidelines

### Test Structure

```
tests/
├── Feature/                   # Integration tests
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── RegisterTest.php
│   │   └── PasswordResetTest.php
│   ├── AssignmentTest.php
│   └── AttendanceTest.php
└── Unit/                      # Unit tests
    ├── Services/
    │   ├── AssignmentServiceTest.php
    │   └── AuthServiceTest.php
    └── Models/
        ├── UserTest.php
        └── AssignmentTest.php
```

### Test Examples

#### Feature Test Example
```php
<?php

use App\Models\User;
use App\Models\Assignment;

it('can create assignment', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->postJson('/api/assignments/store', [
        'title' => 'Test Assignment',
        'description' => 'Test Description',
        'type' => 'homework',
        'subject_id' => 1,
        'section_id' => 1,
        'assigned_session_id' => 1
    ]);

    $response->assertStatus(201)
            ->assertJson([
                'successful' => true,
                'message' => 'تم إنشاء الواجب بنجاح'
            ]);

    $this->assertDatabaseHas('assignments', [
        'title' => 'Test Assignment'
    ]);
});
```

#### Unit Test Example
```php
<?php

use App\Services\AssignmentService;
use App\Models\User;

it('checks permission before creating assignment', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $service = new AssignmentService();
    
    expect(fn() => $service->createAssignment(new Request()))
        ->toThrow(PermissionException::class);
});
```

### Testing Commands

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter AssignmentTest

# Run with coverage
php artisan test --coverage

# Run using Pest
./vendor/bin/pest

# Run specific Pest test
./vendor/bin/pest tests/Feature/AssignmentTest.php
```

---

## Deployment Guide

### Production Environment Setup

#### Server Requirements
- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Nginx/Apache
- SSL Certificate

#### Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=school_production
DB_USERNAME=production_user
DB_PASSWORD=secure_password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=ses
# AWS SES configuration

# File Storage
FILESYSTEM_DISK=s3
# AWS S3 configuration
```

#### Deployment Steps

1. **Server Setup**
```bash
# Install dependencies
sudo apt update
sudo apt install php8.2 php8.2-fpm mysql-server redis-server nginx

# Install PHP extensions
sudo apt install php8.2-mysql php8.2-redis php8.2-mbstring php8.2-xml php8.2-curl
```

2. **Application Deployment**
```bash
# Clone repository
git clone <repository-url> /var/www/school

# Install dependencies
cd /var/www/school
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Environment setup
cp .env.production .env
php artisan key:generate
```

3. **Database Setup**
```bash
php artisan migrate --force
php artisan db:seed --force
```

4. **Optimization**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

5. **Queue Worker Setup**
```bash
# Create systemd service
sudo nano /etc/systemd/system/school-worker.service
```

```ini
[Unit]
Description=School Management Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/school
ExecStart=/usr/bin/php artisan queue:work --daemon
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable school-worker
sudo systemctl start school-worker
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/school/public;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Performance Optimization

### Database Optimization

1. **Indexing Strategy**
```sql
-- User lookup indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_user_type ON users(user_type);

-- Assignment queries
CREATE INDEX idx_assignments_subject_section ON assignments(subject_id, section_id);
CREATE INDEX idx_assignments_created_at ON assignments(created_at);

-- Attendance queries
CREATE INDEX idx_student_attendances_session ON student_attendances(class_session_id);
CREATE INDEX idx_teacher_attendances_date ON teacher_attendances(date);
```

2. **Query Optimization**
```php
// Use eager loading
Assignment::with(['subject', 'section.grade', 'createdBy'])
    ->where('subject_id', $subjectId)
    ->paginate(15);

// Use select to limit columns
User::select(['id', 'first_name', 'last_name', 'email'])
    ->where('user_type', 'teacher')
    ->get();
```

### Caching Strategy

1. **Model Caching**
```php
// Cache expensive queries
Cache::remember('active-users', 3600, function () {
    return User::where('active', true)->count();
});
```

2. **Response Caching**
```php
// Cache API responses
return Cache::remember("assignments.{$subjectId}", 900, function () use ($subjectId) {
    return Assignment::where('subject_id', $subjectId)->get();
});
```

### File Storage Optimization

1. **Use CDN for file delivery**
2. **Implement image resizing**
3. **Use appropriate file formats**

---

## Security Considerations

### Input Validation

1. **Always validate input**
```php
// In Request classes
public function rules(): array
{
    return [
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        'file' => 'required|file|mimes:pdf,doc,docx|max:10240'
    ];
}
```

2. **Sanitize output**
```php
// Use Laravel's built-in escaping
{{ $user->name }} // Automatically escaped
{!! clean($htmlContent) !!} // Use HTML purifier for rich content
```

### Authentication Security

1. **Strong password requirements**
2. **Account lockout after failed attempts**
3. **JWT token expiration**
4. **Rate limiting on auth endpoints**

### File Upload Security

```php
// Validate file types and content
public function rules(): array
{
    return [
        'image' => [
            'required',
            'file',
            'image',
            'mimes:jpeg,png,gif',
            'max:2048',
            Rule::dimensions()->maxWidth(1920)->maxHeight(1080)
        ]
    ];
}
```

### Database Security

1. **Use parameterized queries** (Eloquent does this automatically)
2. **Implement proper access controls**
3. **Regular security updates**
4. **Database backup encryption**

---

## API Best Practices

### RESTful Design

```http
GET    /api/assignments           # List assignments
POST   /api/assignments/store     # Create assignment
GET    /api/assignments/{id}      # Get assignment
PUT    /api/assignments/{id}      # Update assignment
DELETE /api/assignments/{id}      # Delete assignment
```

### Response Consistency

```json
{
  "successful": true,
  "message": "Operation completed successfully",
  "data": {},
  "status_code": 200
}
```

### Error Handling

```php
// Consistent error responses
try {
    $result = $this->performOperation();
    return ResponseHelper::jsonResponse($result, 'Success');
} catch (ModelNotFoundException $e) {
    return ResponseHelper::jsonResponse(null, 'Resource not found', 404, false);
} catch (ValidationException $e) {
    return ResponseHelper::jsonResponse($e->errors(), 'Validation failed', 422, false);
}
```

### Pagination

```php
// Consistent pagination
$items = Model::paginate(15);
return ResponseHelper::jsonResponse($items, 'Items retrieved');
```

---

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
```bash
# Fix file permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

2. **JWT Token Issues**
```bash
# Generate new JWT secret
php artisan jwt:secret

# Clear auth cache
php artisan auth:clear-resets
```

3. **Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

4. **Queue Not Processing**
```bash
# Restart queue worker
sudo systemctl restart school-worker

# Check queue status
php artisan queue:monitor
```

### Debug Mode

```env
# Enable debug mode (development only)
APP_DEBUG=true
LOG_LEVEL=debug
```

### Log Analysis

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log
```

### Performance Debugging

```bash
# Enable query logging
DB::enableQueryLog();
// Your code here
dd(DB::getQueryLog());
```

---

## Contributing Guidelines

### Code Contribution Process

1. **Fork the repository**
2. **Create feature branch**
3. **Implement changes with tests**
4. **Follow coding standards**
5. **Submit pull request**
6. **Address review feedback**

### Documentation Updates

- Update API documentation for new endpoints
- Update component documentation for new classes
- Include usage examples
- Update this developer guide for architectural changes

### Release Process

1. **Version bumping** (semantic versioning)
2. **Changelog updates**
3. **Tag creation**
4. **Production deployment**
5. **Rollback plan preparation**

---

This developer guide provides comprehensive information for setting up, developing, and maintaining the school management system. For specific questions or issues not covered here, please refer to the Laravel documentation or create an issue in the project repository.
