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

    public function addConnection(User $user, User $connectedUser): Connection
    {
        $connection = new Connection();
        $connection->setUser($user);
        $connection->setConnectedUser($connectedUser);
        $connection->setConfirmed(false);
        $this->entityManager->persist($connection);
        $this->entityManager->flush();
        return $connection;
    }

    public function confirmConnection(int $connectionId, User $user): void
    {
        $connection = $this->entityManager->getRepository(Connection::class)->find($connectionId);
        if (!$connection) {
            throw new \Doctrine\ORM\EntityNotFoundException('Connection not found');
        }
        if ($connection->getConnectedUser()?->getId() !== $user->getId()) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException('You are not authorized to confirm this connection');
        }
        $connection->setConfirmed(true);
        $this->entityManager->flush();
    }

    public function deleteConnection(int $connectionId, User $user): void
    {
        $connection = $this->entityManager->getRepository(Connection::class)->find($connectionId);
        if (!$connection) {
            throw new \Doctrine\ORM\EntityNotFoundException('Connection not found');
        }
        // Optionally, add authorization check here
        $this->entityManager->remove($connection);
        $this->entityManager->flush();
    }

}