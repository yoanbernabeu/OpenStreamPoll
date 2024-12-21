<?php

namespace App\Tests\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    /** @var UserPasswordHasherInterface&MockObject */
    private UserPasswordHasherInterface $passwordHasher;

    /** @var UserRepository&MockObject */
    private UserRepository $userRepository;

    private UserService $userService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        
        $this->userService = new UserService(
            $this->entityManager,
            $this->passwordHasher,
            $this->userRepository
        );
    }

    public function testCreateUser(): void
    {
        // Configure Repository mock
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['username' => 'testuser'])
            ->willReturn(null);

        // Configure PasswordHasher mock avec willReturnCallback
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturnCallback(function($user, $password) {
                return 'hashed_' . $password;
            });

        // Configure EntityManager mock
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Test
        $user = $this->userService->createUser('testuser', 'password123');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('hashed_password123', $user->getPassword());
    }

    public function testCreateUserWithExistingUsername(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(new User());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User already exists');

        $this->userService->createUser('existing_user', 'password123');
    }
}
