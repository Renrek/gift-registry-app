<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Connection;
use App\Entity\User;
use App\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ConnectionService
{
    private EntityManagerInterface $entityManager;
    private ConnectionRepository $connectionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConnectionRepository $connectionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->connectionRepository = $connectionRepository;    
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