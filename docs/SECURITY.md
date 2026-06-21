# Security Best Practices & Implementation Guide

## Overview

This document outlines the security measures implemented in SNISTOJ and best practices for maintaining and extending the system.

## 🔐 Core Security Features

### 1. Prepared Statements (SQL Injection Prevention)

**BEFORE (Vulnerable):**

```php
// NEVER DO THIS
$username = $_POST['username'];
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);
```

**AFTER (Secure):**

```php
use SNISTOJ\Config\Database;

$db = Database::getInstance('users');
$user = $db->selectOne('SELECT * FROM users WHERE username = ?', [$username]);
```

### 2. Password Security (Bcrypt Hashing)

**BEFORE (Vulnerable):**

```php
// NEVER DO THIS
$password = md5($_POST['password']); // MD5 is broken
// or
$password = $_POST['password']; // Plaintext storage
```

**AFTER (Secure):**

```php
use SNISTOJ\Utils\Security;

$hashedPassword = Security::hashPassword($_POST['password']);

// Verify during login
if (Security::verifyPassword($_POST['password'], $storedHash)) {
    // Password is correct
}
```

### 3. CSRF Token Protection

**BEFORE (Vulnerable):**

```html
<!-- NEVER DO THIS -->
<form method="POST" action="update.php">
  <input type="text" name="username" />
</form>
```

**AFTER (Secure):**

```php
<?php
use SNISTOJ\Utils\Security;
Security::getCSRFTokenField(); // Output in your form
?>

<form method="POST" action="update.php">
    <?php echo Security::getCSRFTokenField(); ?>
    <input type="text" name="username">
</form>

<!-- In your controller -->
if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    throw new Exception('CSRF token validation failed');
}
```

### 4. Input Validation & Sanitization

**BEFORE (Vulnerable):**

```php
// NEVER DO THIS
$data = $_POST; // Direct use of user input
echo $data['comment']; // XSS vulnerability
```

**AFTER (Secure):**

```php
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Security;

// Validate input
$validator = Validator::make($_POST);
$validator->required('username')
          ->length('username', 3, 20)
          ->required('email')
          ->email('email')
          ->required('password')
          ->length('password', 8, 255);

if ($validator->fails()) {
    $errors = $validator->errors();
    return response('Validation failed', 422);
}

// Sanitize output
echo Security::sanitize($user['comment']);
```

### 5. Secure Headers

The `Security::setSecureHeaders()` method sets critical headers:

```php
X-Content-Type-Options: nosniff          # Prevent MIME type sniffing
X-Frame-Options: DENY                    # Prevent clickjacking
X-XSS-Protection: 1; mode=block          # XSS protection
Strict-Transport-Security: ...           # Force HTTPS
Content-Security-Policy: ...             # Prevent XSS
Referrer-Policy: strict-origin-when-cross-origin
```

### 6. Session Security

**Configuration in bootstrap.php:**

```php
ini_set('session.cookie_httponly', 1);   // Prevent JS access
ini_set('session.cookie_secure', 1);     // HTTPS only
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
```

### 7. Rate Limiting

**Prevent Brute Force Attacks:**

```php
use SNISTOJ\Utils\Security;

if (Security::isRateLimited('login', 10, 60)) {
    // Too many login attempts
    http_response_code(429);
    die('Too many requests. Please try again later.');
}

// Proceed with login
```

### 8. Secure Logging

**Log security events:**

```php
use SNISTOJ\Utils\Logger;

Logger::warning('Failed login attempt', [
    'username' => $username,
    'ip' => Security::getClientIP(),
    'timestamp' => date('Y-m-d H:i:s'),
]);
```

## 🛡️ Security Checklist

### Development

- [ ] Never hardcode database credentials
- [ ] Always use prepared statements
- [ ] Validate all user input
- [ ] Hash passwords with bcrypt
- [ ] Generate CSRF tokens for all forms
- [ ] Sanitize output with `htmlspecialchars()`
- [ ] Log security events
- [ ] Use HTTPS in production
- [ ] Set secure session cookies
- [ ] Implement rate limiting for sensitive endpoints

### Deployment

- [ ] Change default database passwords
- [ ] Generate strong `SECRET_KEY` in `.env`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `APP_ENV=production`
- [ ] Configure firewall rules
- [ ] Enable HTTPS/SSL certificates
- [ ] Set up automated backups
- [ ] Configure log rotation
- [ ] Review file permissions
- [ ] Implement Web Application Firewall (WAF)

### Ongoing

- [ ] Regular security audits
- [ ] Keep PHP and dependencies updated
- [ ] Monitor logs for suspicious activity
- [ ] Regular database backups
- [ ] Security testing before releases
- [ ] Incident response plan
- [ ] User education on passwords
- [ ] Regular penetration testing

## 🔄 Refactoring Guide: Converting Old Code

### Example 1: Converting Database Queries

**Original Code (From old config.php):**

```php
$con = mysqli_connect($host, $user, $pass, $db);
$query = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'";
$result = mysqli_query($con, $query);
```

**Refactored Code:**

```php
<?php
require_once 'bootstrap.php';

use SNISTOJ\Config\Database;
use SNISTOJ\Utils\Logger;

try {
    $db = Database::getInstance('users');
    $user = $db->selectOne('SELECT * FROM users WHERE username = ?', [$_POST['username']]);

    if ($user) {
        Logger::info('User found', ['username' => $_POST['username']]);
    }
} catch (Exception $e) {
    Logger::error('Database error', ['message' => $e->getMessage()]);
    die('An error occurred');
}
```

### Example 2: Converting Authentication

**Original Code:**

```php
session_start();
if (!isset($_SESSION["un"])) {
    header("Location:login.php");
}
```

**Refactored Code:**

```php
<?php
require_once 'bootstrap.php';

use SNISTOJ\Utils\Security;

// Middleware would handle this
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Location: /login');
    exit;
}

// Verify CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}
```

### Example 3: Converting Form Submission

**Original Code:**

```php
<?php
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // No validation, no hashing
    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    mysqli_query($con, $query);
}
?>

<form method="POST">
    <input name="username">
    <input type="password" name="password">
    <button type="submit" name="submit">Register</button>
</form>
```

**Refactored Code:**

```php
<?php
require_once 'bootstrap.php';

use SNISTOJ\Config\Database;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Validator;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }

    // Validate input
    $validator = Validator::make($_POST);
    $validator->required('username')
              ->length('username', 3, 20)
              ->username('username')
              ->required('password')
              ->length('password', 8, 255);

    if ($validator->fails()) {
        $errors = $validator->errors();
        // Show errors to user
    } else {
        // Hash password
        $hashedPassword = Security::hashPassword($_POST['password']);

        // Save to database using prepared statements
        $db = Database::getInstance('users');
        $db->execute_query(
            'INSERT INTO users (username, password) VALUES (?, ?)',
            [$_POST['username'], $hashedPassword]
        );

        header('Location: /login');
        exit;
    }
}
?>

<form method="POST">
    <?php echo Security::getCSRFTokenField(); ?>
    <input type="text" name="username" required>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
```

## 🚨 Common Vulnerabilities & Fixes

### SQL Injection

**Risk**: Attacker modifies query logic  
**Fix**: Use prepared statements

### XSS (Cross-Site Scripting)

**Risk**: Malicious scripts in output  
**Fix**: Sanitize output with `htmlspecialchars()`, set CSP headers

### CSRF (Cross-Site Request Forgery)

**Risk**: Unauthorized actions on behalf of user  
**Fix**: Implement CSRF tokens

### Broken Authentication

**Risk**: Weak password storage, session issues  
**Fix**: Use bcrypt, secure session cookies

### Insecure Direct Object References

**Risk**: Access unauthorized data  
**Fix**: Implement authorization checks in services

### Security Misconfiguration

**Risk**: Debug mode on, default credentials  
**Fix**: Proper .env configuration, security checklist

## 📚 Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [CWE Top 25](https://cwe.mitre.org/top25/)
- [PortSwigger Security Academy](https://portswigger.net/web-security)

---

**Security Version**: 2.0  
**Last Updated**: June 2024
