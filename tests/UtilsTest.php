<?php

namespace SNISTOJ\Tests;

use PHPUnit\Framework\TestCase;
use SNISTOJ\Utils\Security;
use SNISTOJ\Utils\Validator;
use SNISTOJ\Utils\Logger;

/**
 * Security Tests
 */
class SecurityTest extends TestCase
{
    public function testPasswordHashing()
    {
        $password = 'TestPassword123!';
        $hash = Security::hashPassword($password);
        
        $this->assertNotEmpty($hash);
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(Security::verifyPassword($password, $hash));
    }

    public function testPasswordVerificationFails()
    {
        $password = 'CorrectPassword';
        $wrongPassword = 'WrongPassword';
        $hash = Security::hashPassword($password);
        
        $this->assertFalse(Security::verifyPassword($wrongPassword, $hash));
    }

    public function testCSRFTokenGeneration()
    {
        $_SESSION = [];
        
        $token1 = Security::generateCSRFToken();
        $this->assertNotEmpty($token1);
        
        $token2 = Security::generateCSRFToken();
        $this->assertEquals($token1, $token2); // Should be same in same session
    }

    public function testCSRFTokenVerification()
    {
        $_SESSION = [];
        
        $token = Security::generateCSRFToken();
        $this->assertTrue(Security::verifyCSRFToken($token));
        $this->assertFalse(Security::verifyCSRFToken('invalid_token'));
    }

    public function testEmailValidation()
    {
        $this->assertTrue(Security::validateEmail('user@example.com'));
        $this->assertTrue(Security::validateEmail('test.user+tag@domain.co.uk'));
        $this->assertFalse(Security::validateEmail('invalid-email'));
        $this->assertFalse(Security::validateEmail('user@'));
    }

    public function testUsernameValidation()
    {
        $this->assertTrue(Security::validateUsername('john_doe'));
        $this->assertTrue(Security::validateUsername('User123'));
        $this->assertFalse(Security::validateUsername('ab')); // Too short
        $this->assertFalse(Security::validateUsername('user@name')); // Invalid character
    }

    public function testSanitization()
    {
        $input = '<script>alert("XSS")</script>';
        $sanitized = Security::sanitize($input);
        
        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringNotContainsString('</script>', $sanitized);
    }

    public function testTokenGeneration()
    {
        $token1 = Security::generateToken(32);
        $token2 = Security::generateToken(32);
        
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertNotEquals($token1, $token2);
        $this->assertEquals(64, strlen($token1)); // 32 bytes = 64 hex chars
    }

    public function testRateLimiting()
    {
        $_SESSION = [];
        
        // First 3 should pass
        $this->assertFalse(Security::isRateLimited('test', 3, 10));
        $this->assertFalse(Security::isRateLimited('test', 3, 10));
        $this->assertFalse(Security::isRateLimited('test', 3, 10));
        
        // 4th should be limited
        $this->assertTrue(Security::isRateLimited('test', 3, 10));
    }

    public function testClientIPExtraction()
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
        $ip = Security::getClientIP();
        
        $this->assertEquals('192.168.1.1', $ip);
        $this->assertTrue(filter_var($ip, FILTER_VALIDATE_IP));
    }
}

/**
 * Validator Tests
 */
class ValidatorTest extends TestCase
{
    public function testRequiredValidation()
    {
        $validator = Validator::make(['name' => '']);
        $validator->required('name');
        
        $this->assertTrue($validator->fails());
        $this->assertNotEmpty($validator->getErrors('name'));
    }

    public function testLengthValidation()
    {
        $validator = Validator::make(['username' => 'ab']);
        $validator->length('username', 3, 20);
        
        $this->assertTrue($validator->fails());
    }

    public function testNumericValidation()
    {
        $validator = Validator::make(['age' => 'abc']);
        $validator->numeric('age');
        
        $this->assertTrue($validator->fails());
    }

    public function testEmailValidation()
    {
        $validator = Validator::make(['email' => 'invalid']);
        $validator->email('email');
        
        $this->assertTrue($validator->fails());
    }

    public function testUsernameValidation()
    {
        $validator = Validator::make(['username' => 'user@name']);
        $validator->username('username');
        
        $this->assertTrue($validator->fails());
    }

    public function testMatchesValidation()
    {
        $validator = Validator::make([
            'password' => 'pass123',
            'confirm_password' => 'pass456'
        ]);
        $validator->matches('password', 'confirm_password');
        
        $this->assertTrue($validator->fails());
    }

    public function testValidData()
    {
        $validator = Validator::make([
            'username' => 'john_doe',
            'email' => 'john@example.com',
            'age' => 25
        ]);
        $validator->required('username')
                  ->email('email')
                  ->numeric('age');
        
        $this->assertTrue($validator->passes());
    }

    public function testChaining()
    {
        $validator = Validator::make(['password' => 'short']);
        $result = $validator->required('password')
                            ->length('password', 8, 255);
        
        $this->assertInstanceOf(Validator::class, $result);
        $this->assertTrue($validator->fails());
    }
}

/**
 * Logger Tests
 */
class LoggerTest extends TestCase
{
    private $logFile = '/tmp/test-app.log';

    protected function setUp(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testLogging()
    {
        Logger::info('Test message');
        
        $logs = Logger::getRecentLogs(10);
        $this->assertNotEmpty($logs);
    }

    public function testMultipleLevels()
    {
        Logger::debug('Debug message');
        Logger::info('Info message');
        Logger::warning('Warning message');
        Logger::error('Error message');
        Logger::critical('Critical message');
        
        $logs = Logger::getRecentLogs(10);
        $this->assertGreaterThanOrEqual(5, count($logs));
    }

    public function testContextLogging()
    {
        Logger::info('User action', ['user_id' => 123, 'action' => 'login']);
        
        $logs = Logger::getRecentLogs(1);
        $this->assertStringContainsString('User action', $logs[0]);
        $this->assertStringContainsString('user_id', $logs[0]);
    }
}
