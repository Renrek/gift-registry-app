<?php declare(strict_types=1);

namespace App\Controller\Connection;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Connection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

#[Route(path:'/connection')]
class ConnectionController extends AbstractController
{
    
    #[Route(path:'/add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        #needs DTO
        $userId = $request->request->get('user_id');
        $connectedUserId = $request->request->get('connected_user_id');

        $user = $entityManager->getRepository(User::class)->find($userId);
        $connectedUser = $entityManager->getRepository(User::class)->find($connectedUserId);

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
}