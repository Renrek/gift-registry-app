<?php declare(strict_types=1);

namespace App\Controller\Web\Profile;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/profile')]
class ProfileController extends AbstractController
{
    #[Route(path: '', methods: 'GET', name: 'profile')]
    public function index(
        #[CurrentUser] ?User $user,
    ): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        
        
        return $this->render('profile/index.html.twig', [

        ]);
    }
}