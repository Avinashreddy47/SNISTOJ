# Deployment Guide

## Table of Contents

1. [Docker Deployment](#docker-deployment)
2. [VPS/Cloud Deployment](#vpscloud-deployment)
3. [Production Configuration](#production-configuration)
4. [SSL/HTTPS Setup](#sslhttps-setup)
5. [Monitoring & Maintenance](#monitoring--maintenance)

---

## Docker Deployment

### Prerequisites

- Docker and Docker Compose installed
- Domain name (for production)

### Local Development

```bash
# Clone repository
git clone https://github.com/Avinashreddy47/SNISTOJ.git
cd SNISTOJ

# Copy and configure environment
cp .env.example .env

# Start containers
docker-compose up -d

# Wait for services to be healthy
sleep 30

# Initialize database (if needed)
docker-compose exec php php database/seed.php

# Access application
# Web: http://localhost:8080
# PhpMyAdmin: http://localhost:8081
```

### Production Deployment

```bash
# Production environment file
cat > .env.prod << EOF
DB_HOST=mysql_problems
DB_PORT=3306
DB_USER=snistoj
DB_PASSWORD=$(openssl rand -base64 32)
DB_PROBLEMS=vlabproblem
DB_USERS=vlabreg

APP_NAME=SNISTOJ
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SECRET_KEY=$(openssl rand -base64 64)

SESSION_TIMEOUT=3600
COMPILER_TIMEOUT=10
COMPILER_MEMORY_LIMIT=256

LOG_LEVEL=warning
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USER=noreply@yourdomain.com
MAIL_PASSWORD=your-password
EOF

# Copy production config
cp .env.prod .env

# Build for production
docker-compose build

# Start services
docker-compose up -d

# View logs
docker-compose logs -f php

# Create backup
docker-compose exec mysql_problems mysqldump -u snistoj -p vlabproblem > backup_$(date +%Y%m%d).sql
```

### Stopping Services

```bash
# Stop running containers
docker-compose stop

# Remove containers (keep volumes)
docker-compose down

# Remove everything including volumes
docker-compose down -v
```

---

## VPS/Cloud Deployment

### Server Requirements

- Ubuntu 20.04+ or CentOS 8+
- 2GB RAM minimum
- 20GB storage
- Port 80 and 443 available

### Manual Installation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl

# Install Apache
sudo apt install -y apache2 libapache2-mod-php8.2
sudo a2enmod rewrite

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Clone repository
cd /var/www
sudo git clone https://github.com/Avinashreddy47/SNISTOJ.git snistoj
sudo chown -R www-data:www-data snistoj

# Install dependencies (if using composer)
cd snistoj
sudo -u www-data composer install

# Copy and configure environment
sudo -u www-data cp .env.example .env
sudo nano .env
```

### Apache Configuration

```bash
# Create Apache virtual host
sudo tee /etc/apache2/sites-available/snistoj.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com

    DocumentRoot /var/www/snistoj/public

    <Directory /var/www/snistoj/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/snistoj_error.log
    CustomLog \${APACHE_LOG_DIR}/snistoj_access.log combined
</VirtualHost>
EOF

# Enable site
sudo a2ensite snistoj
sudo a2dissite 000-default

# Test Apache configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

### Nginx Configuration (Alternative)

```bash
# Install Nginx
sudo apt install -y nginx php8.2-fpm

# Create Nginx configuration
sudo tee /etc/nginx/sites-available/snistoj > /dev/null <<EOF
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/snistoj/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
EOF

# Enable site
sudo ln -s /etc/nginx/sites-available/snistoj /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default

# Test Nginx
sudo nginx -t

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## Production Configuration

### 1. Environment Variables

Create secure `.env` file:

```bash
# Database
DB_HOST=localhost
DB_PORT=3306
DB_USER=snistoj
DB_PASSWORD=strong-random-password-here

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SECRET_KEY=generated-random-key-here

# Security
SESSION_TIMEOUT=3600

# Logging
LOG_LEVEL=warning
```

### 2. Database Setup

```bash
# Create database and user
sudo mysql << EOF
CREATE DATABASE vlabproblem;
CREATE DATABASE vlabreg;

CREATE USER 'snistoj'@'localhost' IDENTIFIED BY 'strong-password';

GRANT ALL PRIVILEGES ON vlabproblem.* TO 'snistoj'@'localhost';
GRANT ALL PRIVILEGES ON vlabreg.* TO 'snistoj'@'localhost';

FLUSH PRIVILEGES;
EOF
```

### 3. File Permissions

```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/snistoj
sudo find /var/www/snistoj -type f -exec chmod 644 {} \;
sudo find /var/www/snistoj -type d -exec chmod 755 {} \;
sudo chmod 775 /var/www/snistoj/logs

# Protect sensitive files
sudo chmod 600 /var/www/snistoj/.env
```

---

## SSL/HTTPS Setup

### Using Let's Encrypt with Certbot

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Generate certificate
sudo certbot certonly --apache -d yourdomain.com -d www.yourdomain.com

# Create renewal script
sudo tee /etc/letsencrypt/renewal-hooks/post/restart-apache.sh > /dev/null <<EOF
#!/bin/bash
systemctl restart apache2
EOF

sudo chmod +x /etc/letsencrypt/renewal-hooks/post/restart-apache.sh

# Enable auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

### Apache SSL Configuration

```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com

    DocumentRoot /var/www/snistoj/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem

    # Security headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "DENY"

    <Directory /var/www/snistoj/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

---

## Monitoring & Maintenance

### Log Monitoring

```bash
# View Apache logs
sudo tail -f /var/log/apache2/snistoj_access.log
sudo tail -f /var/log/apache2/snistoj_error.log

# View PHP logs
sudo tail -f /var/log/php8.2-fpm.log

# View application logs
tail -f /var/www/snistoj/logs/app.log
```

### Database Backup

```bash
# Manual backup
sudo mysqldump -u snistoj -p vlabproblem > vlabproblem_backup.sql
sudo mysqldump -u snistoj -p vlabreg > vlabreg_backup.sql

# Automated backup script
#!/bin/bash
BACKUP_DIR="/var/backups/snistoj"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
mysqldump -u snistoj -p${DB_PASSWORD} vlabproblem > $BACKUP_DIR/vlabproblem_$DATE.sql
mysqldump -u snistoj -p${DB_PASSWORD} vlabreg > $BACKUP_DIR/vlabreg_$DATE.sql

# Remove old backups (older than 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete

# Add to crontab: 0 2 * * * /opt/snistoj_backup.sh
```

### Performance Monitoring

```bash
# Monitor disk usage
df -h /var/www/snistoj
du -sh /var/www/snistoj

# Monitor memory usage
free -h

# Monitor CPU usage
top

# Monitor MySQL connections
mysql -u snistoj -p -e "SHOW PROCESSLIST;"
```

### System Updates

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update PHP extensions
sudo apt install --only-upgrade php8.2-mysql

# Restart services
sudo systemctl restart apache2
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
```

### Security Hardening

```bash
# Enable firewall
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Disable root login
sudo sed -i 's/^PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sudo systemctl restart ssh

# Configure fail2ban
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## Troubleshooting

### 500 Internal Server Error

```bash
# Check Apache error logs
sudo tail -f /var/log/apache2/snistoj_error.log

# Check PHP error logs
sudo tail -f /var/log/php8.2-fpm.log

# Check file permissions
ls -la /var/www/snistoj/.env
ls -la /var/www/snistoj/logs/
```

### Database Connection Failed

```bash
# Test MySQL connection
mysql -u snistoj -p -h localhost -e "SELECT 1;"

# Check MySQL is running
sudo systemctl status mysql

# Check my.cnf
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

### Application Running Slow

```bash
# Check MySQL slow query log
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Add: slow_query_log = 1
# Add: long_query_time = 2

# Monitor processes
htop

# Check Apache modules
apache2ctl -M
```

---

**Deployment Version**: 2.0  
**Last Updated**: June 2024
