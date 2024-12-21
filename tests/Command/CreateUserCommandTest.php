<?php

namespace App\Tests\Command;

use App\Command\CreateUserCommand;
use App\Service\User\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends TestCase
{
    /** @var UserService&MockObject */
    private UserService $userService;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        
        $command = new CreateUserCommand($this->userService);
        
        $application = new Application();
        $application->add($command);
        
        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccessfully(): void
    {
        $this->userService
            ->expects($this->once())
            ->method('createUser')
            ->with('testuser', 'testpass');

        $this->commandTester->execute([
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $this->assertEquals(0, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('User created', $this->commandTester->getDisplay());
    }

    public function testExecuteWithError(): void
    {
        $this->userService
            ->expects($this->once())
            ->method('createUser')
            ->willThrowException(new \Exception('User already exists'));

        $this->commandTester->execute([
            'username' => 'existing_user',
            'password' => 'testpass',
        ]);

        $this->assertEquals(0, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('User already exists', $this->commandTester->getDisplay());
    }

    public function testExecuteWithMissingArguments(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "username, password")');

        $this->commandTester->execute([]);
    }
}
