# Quick Start Guide

Get SNISTOJ up and running in 5 minutes.

## Option 1: Docker (Fastest)

```bash
# 1. Clone repository
git clone https://github.com/Avinashreddy47/SNISTOJ.git
cd SNISTOJ

# 2. Configure environment
cp .env.example .env

# 3. Start application
docker-compose up -d

# 4. Wait for services
sleep 30

# 5. Access application
# Web: http://localhost:8080
# PhpMyAdmin: http://localhost:8081 (user: snistoj)
```

**Done!** The application is ready to use.

## Option 2: Local Development

```bash
# 1. Clone repository
git clone https://github.com/Avinashreddy47/SNISTOJ.git
cd SNISTOJ

# 2. Configure
cp .env.example .env
nano .env  # Update database credentials

# 3. Install dependencies
composer install

# 4. Set permissions
chmod -R 775 logs/

# 5. Create databases
mysql -u root -p < database/setup.sql

# 6. Run development server
php -S localhost:8000
```

**Open**: http://localhost:8000

## First Steps

### 1. Access the Application

```
Web Interface: http://localhost:8080
PhpMyAdmin: http://localhost:8081
```

### 2. Create Admin User

```bash
# Connect to database
mysql -u snistoj -p vlabreg

# Create admin user
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$12$...', 'admin@example.com', 'admin');
```

### 3. Create Your First Problem

1. Log in as admin
2. Go to "Create Problem"
3. Enter problem title, description
4. Add test cases
5. Publish problem

### 4. Submit a Solution

1. Go to "Problem Archive"
2. Select a problem
3. Write code in your preferred language
4. Click "Submit"
5. View results

## Project Structure Overview

```
SNISTOJ/
├── config/           # Configuration & Database
├── src/              # Application code
│   ├── controllers/  # Request handlers
│   ├── models/       # Data models
│   ├── services/     # Business logic
│   └── utils/        # Helpers
├── public/           # Static files
├── logs/             # Application logs
└── docs/             # Documentation
```

## Common Tasks

### View Application Logs

```bash
# Docker
docker-compose logs -f php

# Local
tail -f logs/app.log
```

### Access Database

```bash
# Docker PhpMyAdmin
Visit: http://localhost:8081

# Local MySQL
mysql -u snistoj -p vlabreg
```

### Stop Services

```bash
# Docker
docker-compose down
```

### Restart Services

```bash
# Docker
docker-compose restart

# Local
# Kill PHP server (Ctrl+C) and restart
```

## Default Credentials

- **PhpMyAdmin Username**: snistoj
- **PhpMyAdmin Password**: Check `.env` file for `DB_PASSWORD`

## Troubleshooting

### Port Already in Use

```bash
# Find process using port 8080
lsof -i :8080

# Kill process
kill -9 <PID>
```

### Database Connection Error

```bash
# Check if MySQL is running
docker-compose ps

# Restart MySQL
docker-compose restart mysql_problems mysql_users

# Check logs
docker-compose logs mysql_problems
```

### Permission Denied

```bash
# Fix file permissions
chmod -R 775 logs/
chmod -R 755 src/ config/
```

## Next Steps

1. **Read Documentation**
   - [Architecture Guide](docs/ARCHITECTURE.md)
   - [Security Guide](docs/SECURITY.md)
   - [Deployment Guide](docs/DEPLOYMENT.md)

2. **Start Development**
   - Create new controllers in `src/controllers/`
   - Add services in `src/services/`
   - Write tests in `tests/`

3. **Deploy to Production**
   - Follow [Deployment Guide](docs/DEPLOYMENT.md)
   - Configure HTTPS
   - Set up monitoring

## Resources

- 📖 [Full Documentation](IMPROVED_README.md)
- 🏗️ [Architecture](docs/ARCHITECTURE.md)
- 🔐 [Security Best Practices](docs/SECURITY.md)
- 🚀 [Deployment](docs/DEPLOYMENT.md)
- 🤝 [Contributing](docs/CONTRIBUTING.md)

---

Need help? Open an issue on [GitHub](https://github.com/Avinashreddy47/SNISTOJ/issues)
