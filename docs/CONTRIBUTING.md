# Contributing to SNISTOJ

Thank you for your interest in contributing to SNISTOJ! We welcome all contributions, from bug reports to feature implementations.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Pull Requests](#pull-requests)
- [Testing](#testing)

---

## Code of Conduct

We are committed to providing a welcoming and inclusive environment for all contributors. Please:

- Be respectful of differing opinions
- Focus on constructive criticism
- Report inappropriate behavior to maintainers
- Treat all community members with respect

---

## Getting Started

### 1. Fork & Clone

```bash
# Fork the repository on GitHub
# Clone your fork
git clone https://github.com/YOUR_USERNAME/SNISTOJ.git
cd SNISTOJ

# Add upstream remote
git remote add upstream https://github.com/Avinashreddy47/SNISTOJ.git
```

### 2. Set Up Development Environment

```bash
# Copy environment file
cp .env.example .env

# Start Docker containers
docker-compose up -d

# Or manual setup
php -S localhost:8000
```

### 3. Create a Branch

```bash
# Update from upstream
git fetch upstream
git rebase upstream/main

# Create feature branch
git checkout -b feature/your-feature-name
git checkout -b bugfix/issue-description
```

---

## Development Workflow

### File Organization

```
src/
├── controllers/  - Request handlers
├── models/       - Data models
├── services/     - Business logic
├── middleware/   - Request middleware
├── utils/        - Utility classes
└── views/        - Templates

config/
├── Config.php    - Configuration manager
├── Database.php  - Database layer
└── Environment.php - Environment loader

tests/
├── Unit/         - Unit tests
├── Integration/  - Integration tests
└── Feature/      - Feature tests
```

### Adding a New Feature

```bash
# 1. Create model
touch src/models/YourModel.php

# 2. Create service
touch src/services/YourService.php

# 3. Create controller
touch src/controllers/YourController.php

# 4. Create test
touch tests/YourFeatureTest.php

# 5. Create view (if needed)
mkdir -p src/views/your-feature
```

---

## Coding Standards

### PHP Standards (PSR-12)

```php
<?php

namespace SNISTOJ\Services;

use SNISTOJ\Config\Database;
use SNISTOJ\Utils\Logger;

/**
 * Class descriptive name
 *
 * Detailed description of what this class does.
 */
class YourService
{
    /**
     * Method description
     *
     * @param string $parameter Description
     * @return bool Result description
     * @throws \Exception If something goes wrong
     */
    public function yourMethod($parameter)
    {
        // Implementation
        return true;
    }

    /**
     * Private method for internal use
     */
    private function helper()
    {
        // Implementation
    }
}
```

### Naming Conventions

- **Classes**: PascalCase (UserService, UserModel)
- **Methods**: camelCase (getUserById, validateInput)
- **Variables**: camelCase (userName, userId)
- **Constants**: UPPER_SNAKE_CASE (MAX_LOGIN_ATTEMPTS, DB_TIMEOUT)
- **Private/Protected**: Prefix with underscore (\_privateMethod)

### Code Style

```php
// Indentation: 4 spaces (no tabs)
public function example()
{
    if ($condition) {
        // Code
    }
}

// Opening braces on same line
class MyClass {

}

// Space after keywords
if ($condition) { }
for ($i = 0; $i < 10; $i++) { }

// Single quotes for strings (unless interpolation needed)
$string = 'Hello';
$dynamic = "Hello $name";
```

### Documentation Standards

Every class and public method should have PHPDoc:

```php
/**
 * Get user by ID
 *
 * Retrieves a user from the database by their ID.
 * Returns null if user not found.
 *
 * @param int $userId The user ID
 * @return array|null User data or null
 * @throws \Exception If database connection fails
 */
public function getUserById($userId)
{
    // Implementation
}
```

---

## Commit Messages

### Format

```
<type>: <subject>

<body>

<footer>
```

### Type

- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Build, CI, dependencies

### Examples

```
feat: Add user registration endpoint

- Implement validation
- Add CSRF protection
- Hash passwords with bcrypt

Closes #123
```

```
fix: Resolve SQL injection vulnerability

The user input was not properly sanitized.
Changed to use prepared statements.

Fixes #456
```

---

## Pull Requests

### Before Submitting

1. **Rebase on upstream main**

   ```bash
   git fetch upstream
   git rebase upstream/main
   git push origin your-branch --force-with-lease
   ```

2. **Run tests**

   ```bash
   composer test
   ```

3. **Check code style**

   ```bash
   composer lint
   ```

4. **Self-review your code**
   - Are there unnecessary comments?
   - Are there any debug statements?
   - Does the code follow standards?

### PR Description Template

```markdown
## Description

Brief description of changes.

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing

Describe testing done.

## Checklist

- [ ] Code follows style guidelines
- [ ] Comments are clear and helpful
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] No breaking changes

## Related Issues

Closes #123
```

### Review Process

1. Maintainer reviews your PR
2. Address any requested changes
3. Re-request review after changes
4. Merge once approved

---

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
composer test tests/UserTest.php

# Run with coverage
composer test:coverage
```

### Writing Tests

```php
<?php

namespace SNISTOJ\Tests;

use PHPUnit\Framework\TestCase;
use SNISTOJ\Services\UserService;

class UserServiceTest extends TestCase
{
    private $userService;

    protected function setUp(): void
    {
        $this->userService = new UserService();
    }

    public function testUserCreation()
    {
        $user = $this->userService->createUser([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user['username']);
    }

    public function testValidationFails()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->userService->createUser([
            'username' => '', // Empty username
            'email' => 'invalid-email',
            'password' => '123' // Too short
        ]);
    }
}
```

### Test Organization

- **Unit Tests**: Test individual methods in isolation
- **Integration Tests**: Test component interactions
- **Feature Tests**: Test complete workflows

---

## Areas for Contribution

### High Priority

- [ ] Unit test coverage
- [ ] Documentation improvements
- [ ] Security audits
- [ ] Performance optimization
- [ ] Bug fixes

### Medium Priority

- [ ] New language support for compiler
- [ ] Enhanced problem management UI
- [ ] Better contest management
- [ ] User statistics dashboard

### Future Features

- [ ] REST API endpoints
- [ ] Real-time code execution feedback
- [ ] Plagiarism detection
- [ ] Machine learning for problem difficulty rating
- [ ] Mobile app

---

## Getting Help

- **Issues**: Ask questions in GitHub issues
- **Discussions**: Use GitHub Discussions for ideas
- **Discord**: Join our community Discord (if available)
- **Email**: avinash@snistoj.com

---

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to SNISTOJ! 🎉

**Contributing Guidelines Version**: 1.0  
**Last Updated**: June 2024
