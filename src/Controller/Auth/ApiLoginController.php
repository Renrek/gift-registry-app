<?php declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route(path: '/api/login', methods: 'POST', name: 'api_login')]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if(null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        //$token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $this->json([
            'user' => $user->getUserIdentifier(),
            //'token' => $token,
        ]);
    }
}