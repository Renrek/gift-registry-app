<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest\v1\Connection;

use App\Controller\Rest\v1\Connection\ConnectionController;
use App\Entity\Connection;
use App\Entity\User;
use App\Formatter\User\UserFormatter;
use App\Repository\ConnectionRepository;
use App\Repository\UserRepository;
use App\Service\ConnectionService;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class ConnectionControllerTest extends TestCase
{
    public function testSearchThrowsWhenUserIsNotLoggedIn(): void
    {
        $controller = $this->createController($this->createMock(ConnectionService::class));

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('You must be logged in to search for a connection.');

        $controller->search(
            new Request(),
            $this->createMock(UserRepository::class),
            $this->createMock(UserFormatter::class),
            $security,
            $this->createMock(ConnectionRepository::class),
        );
    }

    public function testSearchReturnsBadRequestWhenEmailPartialMissing(): void
    {
        $controller = $this->createController($this->createMock(ConnectionService::class));

        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(1);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $response = $controller->search(
            new Request(query: ['emailPartial' => '']),
            $this->createMock(UserRepository::class),
            $this->createMock(UserFormatter::class),
            $security,
            $this->createMock(ConnectionRepository::class),
        );

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('{"error":"Nothing provided to lookup."}', $response->getContent());
    }

    public function testSearchFiltersOutSelfAndExistingConnections(): void
    {
        $controller = $this->createController($this->createMock(ConnectionService::class));

        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(1);

        $selfUser = $this->createMock(User::class);
        $selfUser->method('getId')->willReturn(1);

        $connectedUser = $this->createMock(User::class);
        $connectedUser->method('getId')->willReturn(2);

        $candidateUser = $this->createMock(User::class);
        $candidateUser->method('getId')->willReturn(3);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('searchByEmailPartial')->with('ex')->willReturn([$selfUser, $connectedUser, $candidateUser]);

        $connection = $this->createMock(Connection::class);
        $connection->method('getConnectedUser')->willReturn($connectedUser);
        $connection->method('getUser')->willReturn($currentUser);

        $connectionRepository = $this->createMock(ConnectionRepository::class);
        $connectionRepository->method('getAllConnections')->with($currentUser)->willReturn([$connection]);

        $userFormatter = $this->createMock(UserFormatter::class);
        $userFormatter->expects($this->once())
            ->method('fromEntityList')
            ->with($this->callback(function (array $users): bool {
                return count($users) === 1 && $users[0]->getId() === 3;
            }))
            ->willReturn([['id' => 3, 'email' => 'candidate@example.com']]);

        $response = $controller->search(
            new Request(query: ['emailPartial' => 'ex']),
            $userRepository,
            $userFormatter,
            $security,
            $connectionRepository,
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('[{"id":3,"email":"candidate@example.com"}]', $response->getContent());
    }

    public function testAddReturnsNotFoundWhenTargetUserMissing(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $controller = $this->createController($service);

        $request = new Request(content: json_encode(['id' => 99]));
        $currentUser = $this->createMock(User::class);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('find')->with(99)->willReturn(null);

        $response = $controller->add($request, $currentUser, $userRepository);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('User not found', $response->getContent());
    }

    public function testAddReturnsConflictWhenConnectionAlreadyExists(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $controller = $this->createController($service);

        $request = new Request(content: json_encode(['id' => 2]));
        $currentUser = $this->createMock(User::class);
        $targetUser = $this->createMock(User::class);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('find')->with(2)->willReturn($targetUser);

        $service->method('connectionExists')->with($currentUser, $targetUser)->willReturn(true);

        $response = $controller->add($request, $currentUser, $userRepository);

        $this->assertSame(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertSame('Connection already exists', $response->getContent());
    }

    public function testAddReturnsCreatedWhenConnectionAdded(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $controller = $this->createController($service);

        $request = new Request(content: json_encode(['id' => 2]));
        $currentUser = $this->createMock(User::class);
        $targetUser = $this->createMock(User::class);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('find')->with(2)->willReturn($targetUser);

        $service->method('connectionExists')->with($currentUser, $targetUser)->willReturn(false);
        $service->expects($this->once())->method('addConnection')->with($currentUser, $targetUser);

        $response = $controller->add($request, $currentUser, $userRepository);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertSame('Connection added successfully', $response->getContent());
    }

    public function testConfirmMapsNotFoundExceptionTo404(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $service->method('confirmConnection')->willThrowException(new EntityNotFoundException('Connection not found'));

        $controller = $this->createController($service);

        $response = $controller->confirm(123, $this->createMock(User::class));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('Connection not found', $response->getContent());
    }

    public function testConfirmMapsAccessDeniedExceptionTo403(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $service->method('confirmConnection')->willThrowException(new AccessDeniedException('Denied'));

        $controller = $this->createController($service);

        $response = $controller->confirm(124, $this->createMock(User::class));

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertSame('You are not authorized to confirm this connection', $response->getContent());
    }

    public function testDeleteMapsNotFoundExceptionTo404(): void
    {
        $service = $this->createMock(ConnectionService::class);
        $service->method('deleteConnection')->willThrowException(new EntityNotFoundException('Connection not found'));

        $controller = $this->createController($service);

        $response = $controller->delete(125, $this->createMock(User::class));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame('Connection not found', $response->getContent());
    }

    private function createController(ConnectionService $service): ConnectionController
    {
        return new class($service) extends ConnectionController {
            public function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
            {
                return new JsonResponse($data, $status, $headers);
            }
        };
    }
}
