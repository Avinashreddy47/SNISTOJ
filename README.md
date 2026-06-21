# SNISTOJ - Online Judge System

**S**NISTOJ is a modern, secure, and scalable **Online Judge** system for programming contests, educational institutions, and competitive programming practice.

## 🚀 Features

- **Multi-language Support**: C, C++, C++11, Java, Python
- **Secure Architecture**: Uses prepared statements, CSRF tokens, and bcrypt hashing
- **Real-time Compilation**: Compile and run code instantly
- **Contest Management**: Create and manage programming contests
- **Problem Archive**: Organize and categorize programming problems
- **User Submissions**: Track submissions and statistics
- **Docker Support**: One-command deployment with Docker Compose
- **Environment-based Configuration**: Secure configuration management
- **Comprehensive Logging**: Track all system activities
- **RESTful Architecture**: Modern modular PHP code structure

## 📋 Prerequisites

- **Docker & Docker Compose** (Recommended)
  - [Install Docker](https://docs.docker.com/get-docker/)
  - [Install Docker Compose](https://docs.docker.com/compose/install/)

OR

- **Manual Setup**:
  - PHP 8.2+
  - Apache 2.4+ or Nginx
  - MySQL 8.0+
  - Composer (optional, for dependency management)

## 🏃 Quick Start

### Option 1: Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/Avinashreddy47/SNISTOJ.git
cd SNISTOJ

# Copy environment file
cp .env.example .env

# Edit .env with your database password
nano .env

# Start the application
docker-compose up -d

# Wait for containers to be healthy (30 seconds)
sleep 30

# Access the application
# Web: http://localhost:8080
# PhpMyAdmin: http://localhost:8081
```

### Option 2: Manual Setup

```bash
# Install PHP dependencies (if using composer)
composer install

# Copy and configure environment
cp .env.example .env
nano .env

# Create MySQL databases
mysql -u root -p < database/setup.sql

# Set proper permissions
chmod -R 775 logs/
chmod -R 755 src/ config/

# Run PHP development server
php -S localhost:8000
```

## 📁 Project Structure

```
SNISTOJ/
├── config/
│   ├── Config.php          # Main configuration class
│   ├── Database.php        # Secure database layer
│   └── Environment.php     # Environment variable manager
├── src/
│   ├── controllers/        # Request handlers
│   ├── models/             # Data models
│   ├── services/           # Business logic
│   ├── utils/              # Helper utilities
│   │   ├── Security.php    # Security utilities
│   │   ├── Logger.php      # Logging system
│   │   └── Validator.php   # Input validation
│   ├── middleware/         # Request middleware
│   └── views/              # HTML templates
├── public/                 # Static assets
├── logs/                   # Application logs
├── tests/                  # Unit and integration tests
├── docs/                   # Documentation
├── Dockerfile              # Docker image configuration
├── docker-compose.yml      # Multi-container setup
├── .env.example            # Environment template
└── README.md              # This file
```

## 🔐 Security Features

### Implemented Security Measures

1. **Prepared Statements**: All database queries use prepared statements to prevent SQL injection
2. **CSRF Protection**: CSRF tokens for all form submissions
3. **Password Security**: Bcrypt hashing with cost factor of 12
4. **Input Validation**: Comprehensive input validation framework
5. **XSS Prevention**: HTML entity encoding and Content Security Policy
6. **Secure Headers**: Security headers to prevent common attacks
7. **Rate Limiting**: Basic rate limiting for login attempts
8. **Environment Configuration**: Sensitive data in `.env` file (not in git)

### Security Checklist

- [ ] Update `.env` with strong database passwords
- [ ] Generate new `SECRET_KEY` in `.env`
- [ ] Configure firewall rules for production
- [ ] Enable HTTPS/SSL in production
- [ ] Regular security audits and updates
- [ ] Backup database regularly

## 🔧 Configuration

### Environment Variables

Edit `.env` file to configure the application:

```env
# Database
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=your-strong-password
DB_PROBLEMS=vlabproblem
DB_USERS=vlabreg

# Application
APP_NAME=SNISTOJ
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SECRET_KEY=generate-strong-key-here

# Security
SESSION_TIMEOUT=3600

# Compiler
COMPILER_TIMEOUT=10
COMPILER_MEMORY_LIMIT=256

# Logging
LOG_LEVEL=info
```

## 📊 Database Schema

The system uses two separate MySQL databases:

### `vlabproblem` - Problems Database

- `problems` - Problem statements and test cases
- `submissions` - Code submissions
- `test_cases` - Test cases for problems

### `vlabreg` - User Database

- `users` - User accounts and profiles
- `user_submissions` - User submission tracking
- `sessions` - Session management

## 🚀 Deployment

### Docker Deployment (Production)

```bash
# Build and start with production settings
docker-compose -f docker-compose.yml up -d

# View logs
docker-compose logs -f php

# Stop containers
docker-compose down
```

### Manual Deployment

```bash
# Using Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Configure SSL
sudo certbot certonly --apache -d yourdomain.com

# Set proper permissions
sudo chown -R www-data:www-data /var/www/snistoj
```

## 📝 API Documentation

### Authentication

- User registration and login endpoints
- Session-based authentication
- CSRF token generation

### Problem Endpoints

- GET `/api/problems` - List all problems
- GET `/api/problems/{id}` - Get problem details
- POST `/api/submit` - Submit solution

### Contest Endpoints

- GET `/api/contests` - List contests
- GET `/api/contests/{id}` - Get contest details
- POST `/api/contest/register` - Register for contest

### User Endpoints

- GET `/api/user/profile` - Get user profile
- POST `/api/user/update` - Update user profile
- GET `/api/user/submissions` - Get user submissions

## 📊 Logging

Logs are stored in `/logs/app.log`. Configure log level in `.env`:

```
DEBUG   - Detailed system information
INFO    - General information
WARNING - Warning messages
ERROR   - Error messages
CRITICAL - Critical system failures
```

View recent logs:

```php
use SNISTOJ\Utils\Logger;
$logs = Logger::getRecentLogs(50); // Get last 50 log entries
```

## 🧪 Testing

```bash
# Run unit tests
./vendor/bin/phpunit tests/

# Run specific test
./vendor/bin/phpunit tests/SecurityTest.php
```

## 🐛 Troubleshooting

### Database Connection Issues

```bash
# Check database container logs
docker-compose logs mysql_problems

# Verify database credentials in .env
# Ensure DB_HOST matches service name (mysql_problems)
```

### Permission Denied Errors

```bash
# Fix file permissions
chmod -R 755 src/ config/
chmod -R 775 logs/
chown -R www-data:www-data /var/www/html
```

### Compilation Timeout

- Increase `COMPILER_TIMEOUT` in `.env`
- Check server resources with `docker stats`

## 📚 Advanced Documentation

- [Architecture Guide](docs/ARCHITECTURE.md)
- [Security Best Practices](docs/SECURITY.md)
- [Database Schema](docs/DATABASE.md)
- [API Reference](docs/API.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Contributing Guide](docs/CONTRIBUTING.md)

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](docs/CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- **Avinash Reddy** - Initial work - [GitHub Profile](https://github.com/Avinashreddy47)

## 🙏 Acknowledgments

- Programming contest inspiration from Codeforces, HackerRank, and SPOJ
- PHP security best practices from OWASP
- Community feedback and contributions

## 📞 Support

- GitHub Issues: [Report Issues](https://github.com/Avinashreddy47/SNISTOJ/issues)
- Email: support@snistoj.com
- Documentation: [Online Docs](https://docs.snistoj.com)

---

**Last Updated**: June 2024  
**Version**: 2.0.0
