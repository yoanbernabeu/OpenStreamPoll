<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function createUser(string $username, string $password): ?User
    {
        $existingUser = $this->userRepository->findOneBy(['username' => $username]);
        if ($existingUser) {
            throw new \Exception('User already exists');
        }

        try {
            $this->entityManager->beginTransaction();
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $password));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            throw new \Exception('Error creating user');
        }

        return $user;
    }
}
