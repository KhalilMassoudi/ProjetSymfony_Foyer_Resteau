<?php
namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginSuccessListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof UserInterface) {
            if (method_exists($user, 'incrementLoginCount')) {
                $user->incrementLoginCount();
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Ajouter un log
                $logger = new \Symfony\Component\Console\Logger\ConsoleLogger(new \Symfony\Component\Console\Output\ConsoleOutput());
                $logger->info('Login count incremented', ['user' => $user->getEmail(), 'count' => $user->getLoginCount()]);
            }
        }
    }
}
