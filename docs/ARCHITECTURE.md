# SNISTOJ Architecture Guide

## System Overview

SNISTOJ follows a **layered architecture** pattern with clear separation of concerns:

```
┌─────────────────────────────────────┐
│       Presentation Layer            │
│    (Views & Templates)              │
└────────────┬────────────────────────┘
             │
┌────────────▼────────────────────────┐
│    Application Layer                │
│    (Controllers & Services)         │
└────────────┬────────────────────────┘
             │
┌────────────▼────────────────────────┐
│    Business Logic Layer             │
│    (Services & Models)              │
└────────────┬────────────────────────┘
             │
┌────────────▼────────────────────────┐
│    Data Access Layer                │
│    (Database & ORM)                 │
└─────────────────────────────────────┘
```

## Directory Structure & Components

### `/config` - Configuration Management

**Files:**
- `Environment.php` - Loads `.env` variables
- `Config.php` - Application configuration manager
- `Database.php` - Secure database layer

**Responsibilities:**
- Manage environment variables
- Provide centralized configuration access
- Handle database connections with connection pooling
- Use prepared statements for all queries

**Usage:**
```php
use SNISTOJ\Config\Config;
use SNISTOJ\Config\Database;

// Get configuration
$dbConfig = Config::getDatabase('problems');
$appConfig = Config::getApp();

// Get database connection
$db = Database::getInstance('problems');
$result = $db->selectAll('SELECT * FROM users WHERE active = ?', [1]);
```

### `/src/controllers` - Request Handlers

**Responsibility:** 
- Handle HTTP requests
- Call appropriate services
- Return responses to users
- Validate input and check permissions

**Pattern:**
```php
namespace SNISTOJ\Controllers;

class UserController {
    public function register() {
        // 1. Validate input
        // 2. Call service
        // 3. Return response
    }
    
    public function login() {
        // Handle login logic
    }
}
```

### `/src/models` - Data Models

**Responsibility:**
- Represent database entities
- Provide data structure and validation
- Implement repository pattern

**Example:**
```php
namespace SNISTOJ\Models;

class User {
    public $id;
    public $username;
    public $email;
    
    public function save() { }
    public function delete() { }
}
```

### `/src/services` - Business Logic

**Responsibility:**
- Implement business rules
- Coordinate between models
- Handle transactions
- Call utilities

**Example:**
```php
namespace SNISTOJ\Services;

class UserService {
    public function createUser($data) {
        // Validate
        // Hash password
        // Create user record
        // Log action
    }
}
```

### `/src/utils` - Utility Classes

**Security.php:**
- CSRF token generation/verification
- Password hashing (bcrypt)
- Input sanitization
- Email validation
- Rate limiting

**Logger.php:**
- Log application events
- Support multiple log levels (DEBUG, INFO, WARNING, ERROR, CRITICAL)
- Query recent logs
- Clean old logs

**Validator.php:**
- Validate input data
- Support chainable validation rules
- Collect validation errors
- Support custom messages

**Usage:**
```php
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Logger;

// Security
$hashedPassword = Security::hashPassword('password');
Security::setSecureHeaders();

// Validation
$validator = Validator::make($_POST);
$validator->required('username')
          ->length('username', 3, 20)
          ->required('email')
          ->email('email');

if ($validator->fails()) {
    $errors = $validator->errors();
}

// Logging
Logger::info('User registered', ['username' => 'john']);
```

### `/src/middleware` - Request Middleware

**Responsibility:**
- Authenticate requests
- Check permissions
- Validate CSRF tokens
- Rate limiting
- Logging

**Pattern:**
```php
namespace SNISTOJ\Middleware;

class AuthMiddleware {
    public function handle($request) {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }
    }
}
```

### `/src/views` - Templates

**Responsibility:**
- Display data to users
- Render HTML
- Include UI components

### `/public` - Static Assets

**Structure:**
```
public/
├── css/      - Stylesheets
├── js/       - JavaScript files
├── images/   - Images
└── index.php - Entry point
```

## Request Flow

```
1. Browser Request
        ↓
2. index.php (Router)
        ↓
3. Middleware (Auth, CSRF, Validation)
        ↓
4. Controller (Request Handler)
        ↓
5. Service (Business Logic)
        ↓
6. Model/Database (Data Access)
        ↓
7. Database Query
        ↓
8. Response (View/JSON)
        ↓
9. Browser Display
```

## Security Architecture

### Layers of Protection

**1. Input Layer**
- HTML sanitization
- Type casting
- Prepared statements
- Validator framework

**2. Processing Layer**
- CSRF tokens
- Rate limiting
- Session validation
- Permission checks

**3. Data Layer**
- Encrypted passwords (bcrypt)
- Prepared statements (prevent SQL injection)
- Transactions (data consistency)
- Audit logging

**4. Output Layer**
- HTML entity encoding
- Content Security Policy headers
- XSS protection headers

## Database Design

### Two-Database Architecture

**Problems Database (`vlabproblem`):**
- `problems` - Problem definitions
- `testcases` - Test cases
- `submissions` - Code submissions
- `standings` - Contest standings

**Users Database (`vlabreg`):**
- `users` - User accounts
- `sessions` - User sessions
- `roles` - User roles/permissions
- `audit_log` - Activity tracking

## Configuration Management

### Environment-based Configuration

```
Development (.env):
- APP_DEBUG=true
- DB_LOGGING=enabled
- ERROR_REPORTING=full

Production (.env):
- APP_DEBUG=false
- DB_LOGGING=limited
- ERROR_REPORTING=logs_only
```

### Configuration Access

```php
use SNISTOJ\Config\Config;

// Database
$dbConfig = Config::getDatabase('problems');

// App
$appConfig = Config::getApp();
if (Config::isProduction()) { }

// Session
$sessionConfig = Config::getSession();

// Compiler
$compilerConfig = Config::getCompiler();
```

## Error Handling

### Exception Hierarchy

```php
// Custom exceptions
throw new ValidationException('Username already exists');
throw new AuthenticationException('Invalid credentials');
throw new AuthorizationException('Insufficient permissions');
```

### Global Error Handler

The `ExceptionHandler` class catches all exceptions:
- Logs the error
- Displays appropriate error message
- Returns HTTP error code

## Logging

### Log Levels

1. **DEBUG** - Detailed information for debugging
2. **INFO** - General information about application state
3. **WARNING** - Warning messages (deprecated features, etc.)
4. **ERROR** - Recoverable errors
5. **CRITICAL** - System failures

### Log Storage

Logs are stored in `/logs/app.log` with:
- Timestamp
- Log level
- Message
- Context data (JSON)

### Accessing Logs

```php
use SNISTOJ\Utils\Logger;

// Log messages
Logger::info('User registered', ['user_id' => 123]);

// Get recent logs
$recent = Logger::getRecentLogs(50);

// Clear old logs
Logger::clearOldLogs(30); // Older than 30 days
```

## Extensibility

### Adding New Features

1. **Create Model** (`src/models/Feature.php`)
2. **Create Service** (`src/services/FeatureService.php`)
3. **Create Controller** (`src/controllers/FeatureController.php`)
4. **Create Views** (`src/views/feature/`)
5. **Add Routes** (in routing configuration)
6. **Create Tests** (`tests/FeatureTest.php`)

### Following MVC Pattern

- **Model**: Data representation
- **View**: Presentation
- **Controller**: Orchestration

## Performance Considerations

1. **Database Connection Pooling** - Reuse connections
2. **Prepared Statements** - Improved query performance
3. **Caching** - Cache compiled templates (future)
4. **Logging Levels** - Reduce I/O in production
5. **Compiled Code** - Use opcache in production

## Testing Architecture

### Unit Tests
```php
// Test individual components
class SecurityTest extends PHPUnit_TestCase {
    public function testPasswordHashing() { }
}
```

### Integration Tests
```php
// Test component interactions
class UserRegistrationTest extends PHPUnit_TestCase {
    public function testUserRegistrationFlow() { }
}
```

## Deployment Architecture

### Docker Containers

```
┌─────────────────────────────────────┐
│        PHP-Apache Container         │
│   (Application & Web Server)        │
└──────────────┬──────────────────────┘
               │
        ┌──────┴──────┐
        │             │
┌───────▼──────┐  ┌───▼────────┐
│  MySQL       │  │  MySQL     │
│  (Problems)  │  │  (Users)   │
└──────────────┘  └────────────┘
```

---

**Architecture Version**: 2.0  
**Last Updated**: June 2024
