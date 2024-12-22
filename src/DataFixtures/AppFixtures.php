<?php

namespace App\DataFixtures;

use App\Entity\Poll;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadPolls($manager);
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin', 'adminpass', ['ROLE_ADMIN']);
        $manager->persist($admin);

        $user = $this->createUser('user', 'userpass', ['ROLE_USER']);
        $manager->persist($user);
    }

    /**
     * @param list<string> $roles
     */
    private function createUser(string $username, string $password, array $roles): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles($roles);

        return $user;
    }

    private function loadPolls(ObjectManager $manager): void
    {
        $activePoll = $this->createActivePoll();
        $manager->persist($activePoll);

        $expiredPoll = $this->createExpiredPoll();
        $manager->persist($expiredPoll);

        // Créer plus de votes pour le sondage actif
        $this->createVotesForPoll($manager, $activePoll, 10);
        // Quelques votes pour le sondage expiré
        $this->createVotesForPoll($manager, $expiredPoll, 5);
    }

    private function createActivePoll(): Poll
    {
        $poll = new Poll();
        $poll
            ->setTitle('Active Poll')
            ->setShortCode('abc123')
            ->setStartAt(new \DateTimeImmutable('now'))
            ->setEndAt(new \DateTimeImmutable('+1 hour'))
            ->setQuestion1('Choice 1')
            ->setQuestion2('Choice 2')
            ->setQuestion3('Choice 3');

        return $poll;
    }

    private function createExpiredPoll(): Poll
    {
        $poll = new Poll();
        $poll
            ->setTitle('Expired Poll')
            ->setShortCode('xyz789')
            ->setStartAt(new \DateTimeImmutable('-2 hours'))
            ->setEndAt(new \DateTimeImmutable('-1 hour'))
            ->setQuestion1('Choice 1')
            ->setQuestion2('Choice 2');

        return $poll;
    }

    private function createVotesForPoll(ObjectManager $manager, Poll $poll, int $numberOfVotes): void
    {
        for ($i = 1; $i <= $numberOfVotes; ++$i) {
            $vote = new Vote();
            $vote->setPoll($poll);
            $vote->setChoice(($i % 3) + 1); // Répartir les votes entre les choix 1, 2 et 3
            $vote->setVoterId('visitor_'.$i);
            $vote->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($vote);
        }
    }
}
