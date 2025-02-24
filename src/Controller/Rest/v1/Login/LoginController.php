<?php declare(strict_types=1);

namespace App\Controller\Rest\v1\Login;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/api/v1/login')]
class LoginController extends AbstractController
{
    #[Route(path: '', methods: 'POST', name: 'api_v1_login')]
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

    // #[Route('/api/logout', name: 'app_logout')]
    // public function handleLogout(Security $security): Response
    // {
    //     $response = $security->logout();
    //     return $this->redirect('/');
    // }
}