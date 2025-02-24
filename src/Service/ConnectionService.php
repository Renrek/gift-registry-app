<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Connection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ConnectionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;   
    }

    public function connectionExists(User $user, User $connectedUser): bool
    {
        $existingConnection = $this->entityManager->getRepository(Connection::class)->findOneBy([
            'user' => $user,
            'connectedUser' => $connectedUser
        ]);

        $inverseConnection = $this->entityManager->getRepository(Connection::class)->findOneBy([
            'user' => $connectedUser,
            'connectedUser' => $user
        ]);

        return $existingConnection !== null || $inverseConnection !== null;
    }

}