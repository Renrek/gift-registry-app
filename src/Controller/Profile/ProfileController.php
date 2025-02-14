<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\Invitation\InvitationFormatter; 
use App\Entity\User;
use App\Feature\InvitationPanel\DTOs\InvitationPanelConfig;
use App\Repository\ConnectionRepository;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityNotFoundException;
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
    ): Response
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $invitations = $invitationRepository->findBy(['inviter' => $user->getId()]);
        $invitationList = $invitationFormatter->fromModels($invitations);

        // Get connections where the current user is either the inviter or the invitee
        // $connectionsInitiated = $user->getConnectionsInitiated();
        // $connectionsReceived = $user->getConnectionsReceived();
        // $connections = array_merge($connectionsInitiated->toArray(), $connectionsReceived->toArray());
        // dd($connections);

        // Get connections where the current user is either the inviter or the invitee
        $connections = $connectionRepository->findByUser($user);
        //dd($connections);
        // Extract connected users
        
        $connectedUsers = [];
        foreach ($connections as $connection) {
            $connectedUser = $connection->getUser();
            if(!$connectedUser) {
                throw new EntityNotFoundException('Connected user not found');
            }
            if ($connectedUser->getId() === $user->getId()) {
                $connectedUsers[] = $connection->getConnectedUser();
            } else {
                $connectedUsers[] = $connectedUser;
            }
        }

        return $this->render('profile/index.html.twig', [
            'invitationConfig' => new InvitationPanelConfig(
                createInvitationUrl: $this->generateUrl('create_invitation'),
                invitationList: $invitationList,
            ),
            'contacts' => $connectedUsers,
        ]);
    }
}