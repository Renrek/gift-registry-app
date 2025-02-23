<?php declare(strict_types=1);

namespace App\Controller\Web\Profile;

use App\Controller\Web\Connection\ConnectionFormatter;
use App\Controller\Web\Invitation\InvitationFormatter;
use App\Controller\Web\User\DTOs\UserDTO;
use App\Entity\User;
use App\Feature\ConnectionPanel\ConnectionPanelFormatter;
use App\Feature\ConnectionPanel\DTOs\ConnectionPanelConfig;
use App\Feature\InvitationPanel\DTOs\InvitationPanelConfig;
use App\Repository\ConnectionRepository;
use App\Repository\InvitationRepository;
use App\Service\ConnectionService;
use Doctrine\ORM\EntityNotFoundException;
use FTP\Connection;
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
        InvitationFormatter $invitationFormatter,
        InvitationRepository $invitationRepository,
        ConnectionRepository $connectionRepository,
        ConnectionPanelFormatter $connectionPanelFormatter,
    ): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $invitations = $invitationRepository->findBy(['inviter' => $user->getId()]);
        $invitationList = $invitationFormatter->fromModels($invitations);

        $invitationConfig = new InvitationPanelConfig(
            createInvitationUrl: $this->generateUrl('create_invitation'),
            invitationList: $invitationList,
        );

        $connections = $connectionRepository->findByUser($user);

        $connectionConfig = new ConnectionPanelConfig(
            searchUrl: $this->generateUrl('search_connections'),
            addUrl: $this->generateUrl('add_connection'),
            connectedUsers: $connectionPanelFormatter->fromModelList($connections),
        );
        
        return $this->render('profile/index.html.twig', [
            'invitationConfig' => $invitationConfig,
            'connectionConfig' => $connectionConfig,
        ]);
    }
}