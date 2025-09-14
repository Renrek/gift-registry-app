<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Connection;
use App\Entity\User;
use App\Service\ConnectionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ConnectionServiceTest extends TestCase
{
    public function testConnectionExistsReturnsTrueForDirectConnection(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);

        /** @var User&MockObject $connectedUser */
        $connectedUser = $this->createMock(User::class);

        $connection = $this->createMock(Connection::class);

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [['user' => $user, 'connectedUser' => $connectedUser]],
                [['user' => $connectedUser, 'connectedUser' => $user]]
            )
            ->willReturnOnConsecutiveCalls($connection, null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        /** @var EntityManagerInterface&MockObject $entityManager */
        $service = new ConnectionService($entityManager);

        $this->assertTrue($service->connectionExists($user, $connectedUser));
    }

    public function testConnectionExistsReturnsTrueForInverseConnection(): void
    {
        $user = $this->createMock(User::class);
        $connectedUser = $this->createMock(User::class);

        $connection = $this->createMock(Connection::class);

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [['user' => $user, 'connectedUser' => $connectedUser]],
                [['user' => $connectedUser, 'connectedUser' => $user]]
            )
            ->willReturnOnConsecutiveCalls(null, $connection);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->assertTrue($service->connectionExists($user, $connectedUser));
    }

    public function testConnectionExistsReturnsFalseWhenNoConnection(): void
    {
        $user = $this->createMock(User::class);
        $connectedUser = $this->createMock(User::class);

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [['user' => $user, 'connectedUser' => $connectedUser]],
                [['user' => $connectedUser, 'connectedUser' => $user]]
            )
            ->willReturnOnConsecutiveCalls(null, null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->assertFalse($service->connectionExists($user, $connectedUser));
    }
}