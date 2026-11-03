<?php declare(strict_types=1);

namespace App\Controller\Web\Home;

use App\Entity\User;
use App\Feature\ConnectionPanel\DTO\ConnectionPanelConfig;
use App\Feature\ConnectionPanel\Formatter\ConnectionPanelFormatter;
use App\Repository\ConnectionRepository;
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

        $connections = $connectionRepository->findByUser($user);

        $connectionConfig = new ConnectionPanelConfig(
            searchUrl: $this->generateUrl('api_v1_search_connections'),
            addUrl: $this->generateUrl('api_v1_add_connection'),
            connectedUsers: $connectionPanelFormatter->forContactsPanel($connections),
        );
        
        return $this->render('home/index.html.twig', [
            'connectionConfig' => $connectionConfig,
        ]);
    }
}