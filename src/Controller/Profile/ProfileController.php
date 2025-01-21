<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\Profile\DTOs\InvitationFormatter;
use App\Entity\User;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/profile')]
class ProfileController extends AbstractController
{
    #[Route(path: '', methods: 'GET')]
    public function index(
        #[CurrentUser] ?User $user,
        InvitationFormatter $invitationFormatter,
        InvitationRepository $invitationRepository,
    ): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $invitations = $invitationRepository->findBy(['inviter' => $user->getId()]);
        $invitationList = $invitationFormatter->fromModels($invitations);

        return $this->render('profile/index.html.twig', [
            'invitationList' => $invitationList,
        ]);
    }
}