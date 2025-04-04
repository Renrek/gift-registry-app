<?php declare(strict_types=1);

namespace App\Controller\Web\Home;

use App\Entity\User;
use App\Feature\ConnectionPanel\DTO\ConnectionPanelConfig;
use App\Feature\ConnectionPanel\Formatter\ConnectionPanelFormatter;
use App\Feature\InvitationPanel\DTOs\InvitationPanelConfig;
use App\Formatter\Invitation\InvitationFormatter;
use App\Repository\ConnectionRepository;
use App\Repository\InvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'')]
class HomeController extends AbstractController
{
    #[Route(path:'', methods: 'GET', name: 'home')]
    public function index(
        #[CurrentUser()] ?User $user,
        InvitationFormatter $invitationFormatter,
        InvitationRepository $invitationRepository,
        ConnectionRepository $connectionRepository,
        ConnectionPanelFormatter $connectionPanelFormatter,
    ): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        $isLoggedin = $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');

        if (!$isLoggedin) {
            return $this->render('home/login.html.twig', [
                'isLoggedin' => $isLoggedin,
            ]);
        }

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $invitations = $invitationRepository->findBy([
            'inviter' => $user->getId(),
            'used' => false,
        ]);
        
        $invitationList = $invitationFormatter->forInvitationPanel($invitations);

        $invitationConfig = new InvitationPanelConfig(
            createInvitationUrl: $this->generateUrl('api_v1_create_invitation'),
            invitationList: $invitationList,
        );

        $connections = $connectionRepository->findByUser($user);

        $connectionConfig = new ConnectionPanelConfig(
            searchUrl: $this->generateUrl('api_v1_search_connections'),
            addUrl: $this->generateUrl('api_v1_add_connection'),
            connectedUsers: $connectionPanelFormatter->forContactsPanel($connections),
        );
        
        return $this->render('home/index.html.twig', [ 
            'invitationConfig' => $invitationConfig,
            'connectionConfig' => $connectionConfig,
        ]);
    }
}