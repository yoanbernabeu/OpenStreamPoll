<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testGetUserIdentifier(): void
    {
        $this->user->setUsername('testuser');
        $this->assertEquals('testuser', $this->user->getUserIdentifier());
    }

    public function testDefaultRoles(): void
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }

    public function testCustomRoles(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $roles = $this->user->getRoles();
        
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertCount(2, $roles);
    }

    public function testEmptyUsernameReturnsAnonymous(): void
    {
        $this->assertEquals('anonymous', $this->user->getUserIdentifier());
    }
}
