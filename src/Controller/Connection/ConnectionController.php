<?php declare(strict_types=1);

namespace App\Controller\Connection;

use App\Controller\User\DTOs\UserFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Connection;
use App\Entity\User;
use App\Repository\ConnectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(path:'/connection')]
class ConnectionController extends AbstractController
{
    
    #[Route(path: '/search/{emailPartial?}', methods: ['GET'])]
    public function search(
        string $emailPartial, 
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

        if (!$emailPartial || trim($emailPartial) === '') {
            return $this->json(['error' => 'Nothing provided to lookup.'], 400);
        }

        $users = $userRepository->searchByEmailPartial($emailPartial);

        $users = array_values(array_filter($users, fn($u) => $u->getId() !== $user->getId()));

        $existingConnections = $connectionRepository->findByUser($user);
        $existingConnectionIds = array_map(fn($c) => $c->getConnectedUser()->getId(), $existingConnections);

        $remainingUsers = array_values(array_filter($users, fn($u) => !in_array($u->getId(), $existingConnectionIds)));

        $remainingUsers = $userFormatter->fromEntityList($remainingUsers);

        return $this->json($remainingUsers, 200);
    }

    #[Route(path:'/add', methods: ['POST'])]
    public function add(
        Request $request, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        
        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException('You must be logged in to confirm a connection.');
        }

        $payload = json_decode($request->getContent(), false);
        
        $connectedUser = $entityManager->getRepository(User::class)->find($payload->id);

        if (!$user || !$connectedUser) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $connection = new Connection();
        $connection->setUser($user);
        $connection->setConnectedUser($connectedUser);
        $connection->setConfirmed(false);

        $entityManager->persist($connection);
        $entityManager->flush();

        return new Response('Connection added successfully', Response::HTTP_CREATED);
    }

    #[Route(path:'/confirm/{connectionId}', methods: ['POST'])]
    public function confirm(
        int $connectionId, 
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {

        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException('You must be logged in to confirm a connection.');
        }

        $connection = $entityManager->getRepository(Connection::class)->find($connectionId);

        if (!$connection) {
            return new Response('Connection not found', Response::HTTP_NOT_FOUND);
        }

        $connection->setConfirmed(true);

        $entityManager->flush();

        return new Response('Connection confirmed successfully', Response::HTTP_OK);
    }


}