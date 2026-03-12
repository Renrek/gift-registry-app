<?php declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;

use App\Entity\Connection;
use App\Entity\User;
use App\Service\ConnectionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ConnectionServiceTest extends TestCase
{
    public function testConnectionExistsReturnsTrueForDirectConnection(): void
    {
        $user = new User();
        $connectedUser = new User();

        $connection = $this->createMock(Connection::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->with($this->callback(function ($arg) use ($user, $connectedUser) {
                return (
                    $arg === ['user' => $user, 'connectedUser' => $connectedUser] ||
                    $arg === ['user' => $connectedUser, 'connectedUser' => $user]
                );
            }))
            ->willReturnOnConsecutiveCalls($connection, null);

    
    $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->assertTrue($service->connectionExists($user, $connectedUser));
    }

    public function testConnectionExistsReturnsTrueForInverseConnection(): void
    {
    $user = new User();
    $connectedUser = new User();

        $connection = $this->createMock(Connection::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->with($this->callback(function ($arg) use ($user, $connectedUser) {
                return (
                    $arg === ['user' => $user, 'connectedUser' => $connectedUser] ||
                    $arg === ['user' => $connectedUser, 'connectedUser' => $user]
                );
            }))
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
    $user = new User();
    $connectedUser = new User();

        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->exactly(2))
            ->method('findOneBy')
            ->with($this->callback(function ($arg) use ($user, $connectedUser) {
                return (
                    $arg === ['user' => $user, 'connectedUser' => $connectedUser] ||
                    $arg === ['user' => $connectedUser, 'connectedUser' => $user]
                );
            }))
            ->willReturnOnConsecutiveCalls(null, null);

    
    $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->assertFalse($service->connectionExists($user, $connectedUser));
    }

    public function testConfirmConnectionThrowsWhenConnectionNotFound(): void
    {
        $connectionId = 10;

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($connectionId)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Connection not found');

        $service->confirmConnection($connectionId, $this->createMock(User::class));
    }

    public function testConfirmConnectionThrowsWhenUserIsNotConnectedUser(): void
    {
        $connectionId = 11;

        $connectedUser = $this->createMock(User::class);
        $connectedUser->method('getId')->willReturn(200);

        $actingUser = $this->createMock(User::class);
        $actingUser->method('getId')->willReturn(100);

        $connection = $this->createMock(Connection::class);
        $connection->method('getConnectedUser')->willReturn($connectedUser);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($connectionId)->willReturn($connection);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('You are not authorized to confirm this connection');

        $service->confirmConnection($connectionId, $actingUser);
    }

    public function testConfirmConnectionSetsConfirmedAndFlushesForAuthorizedUser(): void
    {
        $connectionId = 12;

        $connectedUser = $this->createMock(User::class);
        $connectedUser->method('getId')->willReturn(300);

        $actingUser = $this->createMock(User::class);
        $actingUser->method('getId')->willReturn(300);

        $connection = $this->createMock(Connection::class);
        $connection->method('getConnectedUser')->willReturn($connectedUser);
        $connection->expects($this->once())->method('setConfirmed')->with(true);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($connectionId)->willReturn($connection);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())->method('flush');

        $service = new ConnectionService($entityManager);
        $service->confirmConnection($connectionId, $actingUser);
    }

    public function testDeleteConnectionThrowsWhenConnectionNotFound(): void
    {
        $connectionId = 13;

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($connectionId)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);

        $service = new ConnectionService($entityManager);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Connection not found');

        $service->deleteConnection($connectionId, $this->createMock(User::class));
    }

    public function testDeleteConnectionRemovesEntityAndFlushes(): void
    {
        $connectionId = 14;
        $connection = $this->createMock(Connection::class);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($connectionId)->willReturn($connection);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')
            ->with(Connection::class)
            ->willReturn($repository);
        $entityManager->expects($this->once())->method('remove')->with($connection);
        $entityManager->expects($this->once())->method('flush');

        $service = new ConnectionService($entityManager);
        $service->deleteConnection($connectionId, $this->createMock(User::class));
    }
}