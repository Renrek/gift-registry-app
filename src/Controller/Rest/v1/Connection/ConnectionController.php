<?php declare(strict_types=1);

namespace App\Controller\Rest\v1\Connection;

use App\Formatter\User\UserFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ConnectionRepository;
use App\Repository\UserRepository;
use App\Service\ConnectionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/api/v1/connections')]
class ConnectionController extends AbstractController
{
    public function __construct(
        public ConnectionService $connectionService,
    ){}

    #[Route(path: '/search', methods: ['GET'], name: 'api_v1_search_connections')]
    public function search( 
        Request $request,
        UserRepository $userRepository,
        UserFormatter $userFormatter,
        Security $security,
        ConnectionRepository $connectionRepository
    ): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException('You must be logged in to search for a connection.');
        }

        $emailPartial = $request->query->get('emailPartial', '');
        
        if (!is_string($emailPartial) || trim($emailPartial) === '') {
            return $this->json(['error' => 'Nothing provided to lookup.'], 400);
        }

        $users = $userRepository->searchByEmailPartial($emailPartial);

        $users = array_values(array_filter($users, fn($u) => $u->getId() !== $user->getId()));

        $connections = $connectionRepository->getAllConnections($user);
        
        $connectionIds = array_map(fn($c) => $c->getConnectedUser()?->getId(), $connections);
        $inverseConnectionIds = array_map(fn($c) => $c->getUser()?->getId(), $connections);
        $allConnectionIds = array_unique(array_merge($connectionIds, $inverseConnectionIds));
        
        $remainingUsers = array_values(array_filter($users, fn($u) => !in_array($u->getId(), $allConnectionIds)));
        
        $remainingUsers = $userFormatter->fromEntityList($remainingUsers);

        return $this->json($remainingUsers, 200);
    }

    #[Route(path:'/add', methods: ['POST'], name: 'api_v1_add_connection')]
    public function add(
        Request $request,
        #[CurrentUser] ?User $user,
        UserRepository $userRepository
    ): Response {
        if (!$user) {
            throw new AccessDeniedException('You must be logged in to confirm a connection.');
        }
        $payload = json_decode($request->getContent(), false);
        $connectedUser = $userRepository->find($payload->id ?? null);
        if (!$connectedUser) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }
        if ($this->connectionService->connectionExists($user, $connectedUser)) {
            return new Response('Connection already exists', Response::HTTP_CONFLICT);
        }
        $this->connectionService->addConnection($user, $connectedUser);
        return new Response('Connection added successfully', Response::HTTP_CREATED);
    }

    #[Route(path:'/{connectionId}/confirm', methods: ['POST'], name: 'api_v1_confirm_connection')]
    public function confirm(
        int $connectionId,
        #[CurrentUser] ?User $user
    ): Response {
        if (!$user) {
            throw new AccessDeniedException('You must be logged in to confirm a connection.');
        }
        try {
            $this->connectionService->confirmConnection($connectionId, $user);
        } catch (\Doctrine\ORM\EntityNotFoundException) {
            return new Response('Connection not found', Response::HTTP_NOT_FOUND);
        } catch (\Symfony\Component\Security\Core\Exception\AccessDeniedException) {
            return new Response('You are not authorized to confirm this connection', Response::HTTP_FORBIDDEN);
        }
        return new Response('Connection confirmed successfully', Response::HTTP_OK);
    }

    #[Route(path:'/{connectionId}/delete', methods: ['DELETE'], name: 'api_v1_delete_connection')]
    public function delete(
        int $connectionId,
        #[CurrentUser] ?User $user
    ): Response {
        if (!$user) {
            throw new AccessDeniedException('You must be logged in to delete a connection.');
        }
        try {
            $this->connectionService->deleteConnection($connectionId, $user);
        } catch (\Doctrine\ORM\EntityNotFoundException) {
            return new Response('Connection not found', Response::HTTP_NOT_FOUND);
        }
        return new Response('Connection deleted successfully', Response::HTTP_OK);
    }

}