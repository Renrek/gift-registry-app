<?php declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\User\DTOs\UserFormatter;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/users')]
class UserController extends AbstractController
{
    #[Route(path: '/search/{emailPartial}', methods: ['GET'])]
    public function search(
        string $emailPartial, 
        UserRepository $userRepository,
        UserFormatter $userFormatter,
    ): Response
    {
        
        if ($emailPartial === '') {
            return $this->json(['error' => 'Nothing provided to lookup.'], 400);
        }

        $users = $userRepository->searchByEmailPartial($emailPartial);
        $users = array_map(fn($user) => $userFormatter->fromEntity($user), $users);
        $users = $userFormatter->fromEntityList($users);
        return $this->json($users, 200);
    }
    
}