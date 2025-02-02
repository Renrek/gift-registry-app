<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\Profile\DTOs\InvitationFormatter;
use App\Entity\User;
use App\Repository\ConnectionRepository;
use App\Repository\InvitationRepository;
use FTP\Connection;
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
            if ($connection->getUser()->getId() === $user->getId()) {
                $connectedUsers[] = $connection->getConnectedUser();
            } else {
                $connectedUsers[] = $connection->getUser();
            }
        }

             //dd($connectedUsers);

        return $this->render('profile/index.html.twig', [
            'invitationList' => $invitationList,
            'contacts' => $connectedUsers,
        ]);
    }
}