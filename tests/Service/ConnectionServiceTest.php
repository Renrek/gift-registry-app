<?php declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;

use App\Entity\Connection;
use App\Entity\User;
use App\Service\ConnectionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

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
}