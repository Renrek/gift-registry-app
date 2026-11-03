<?php declare(strict_types=1);

namespace App\Controller\Web\Profile;

use App\Entity\User;
use App\Feature\InvitationPanel\DTOs\InvitationPanelConfig;
use App\Formatter\Invitation\InvitationFormatter;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/profile')]
class ProfileController extends AbstractController
{
    #[Route(path: '', methods: 'GET', name: 'profile')]
    public function index(
        #[CurrentUser] ?User $user,
        InvitationFormatter $invitationFormatter,
        InvitationRepository $invitationRepository,
    ): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $invitations = $invitationRepository->findBy([
            'inviter' => $user->getId(),
            'used' => false,
        ]);

        $invitationConfig = new InvitationPanelConfig(
            createInvitationUrl: $this->generateUrl('api_v1_create_invitation'),
            invitationList: $invitationFormatter->forInvitationPanel($invitations),
        );

        return $this->render('profile/index.html.twig', [
            'invitationConfig' => $invitationConfig,
        ]);
    }
}