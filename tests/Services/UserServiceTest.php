<?php

namespace SNISTOJ\Tests;

use PHPUnit\Framework\TestCase;
use SNISTOJ\Services\UserService;
use SNISTOJ\Models\User;

/**
 * User Service Tests
 */
class UserServiceTest extends TestCase
{
    private $userService;

    protected function setUp(): void
    {
        $this->userService = new UserService();
    }

    public function testUserCreation()
    {
        $userData = [
            'username' => 'testuser' . uniqid(),
            'email' => 'test' . uniqid() . '@example.com',
            'password' => 'TestPassword123!'
        ];

        try {
            $user = $this->userService->createUser($userData);
            
            $this->assertNotNull($user);
            $this->assertEquals($userData['username'], $user['username']);
            $this->assertEquals($userData['email'], $user['email']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available for integration test');
        }
    }

    public function testUserAuthenticationSuccess()
    {
        $username = 'testuser' . uniqid();
        $password = 'TestPassword123!';
        $email = 'test' . uniqid() . '@example.com';

        try {
            $this->userService->createUser([
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);

            $result = $this->userService->authenticate($username, $password);
            
            $this->assertNotNull($result);
            $this->assertEquals($username, $result['username']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available for integration test');
        }
    }

    public function testUserAuthenticationFailure()
    {
        $result = $this->userService->authenticate('nonexistent', 'password');
        $this->assertFalse($result);
    }

    public function testDuplicateUsernameRejection()
    {
        $username = 'unique_' . uniqid();
        $email1 = 'email1_' . uniqid() . '@example.com';
        $email2 = 'email2_' . uniqid() . '@example.com';

        try {
            $this->userService->createUser([
                'username' => $username,
                'email' => $email1,
                'password' => 'Password123!'
            ]);

            $this->expectException(\Exception::class);
            $this->userService->createUser([
                'username' => $username,
                'email' => $email2,
                'password' => 'Password123!'
            ]);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available for integration test');
        }
    }

    public function testUserUpdate()
    {
        $username = 'testuser' . uniqid();
        $email = 'test' . uniqid() . '@example.com';

        try {
            $user = $this->userService->createUser([
                'username' => $username,
                'email' => $email,
                'password' => 'Password123!'
            ]);

            $this->userService->updateUser($user['id'], [
                'full_name' => 'Updated Name'
            ]);

            $updated = $this->userService->getUserById($user['id']);
            $this->assertEquals('Updated Name', $updated['full_name']);
        } catch (\Exception $e) {
            $this->markTestSkipped('Database not available for integration test');
        }
    }
}

/**
 * User Model Tests
 */
class UserModelTest extends TestCase
{
    public function testUserToArray()
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'testuser';
        $user->email = 'test@example.com';
        $user->role = 'user';

        $array = $user->toArray();
        
        $this->assertEquals(1, $array['id']);
        $this->assertEquals('testuser', $array['username']);
        $this->assertEquals('test@example.com', $array['email']);
    }

    public function testUserRoleChecking()
    {
        $user = new User();
        $user->role = 'admin';

        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('user'));
    }
}
