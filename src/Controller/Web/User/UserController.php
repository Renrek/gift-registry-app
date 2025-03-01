<?php declare(strict_types=1);

namespace App\Controller\Web\User;

use App\Controller\Web\User\DTOs\UserFormatter;
use App\Entity\User;
use App\Feature\GiftSelectionPanel\DTO\GiftSelectionPanelConfig;
use App\Feature\GiftSelectionPanel\Formatter\GiftSelectionPanelFormatter;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/users')]
class UserController extends AbstractController
{
    #[Route(path: '/{id}/view', methods: ['GET'], name: 'get_user_view')]
    public function handleGetUserView(
        int $id,
        EntityManagerInterface $entityManager,
        GiftSelectionPanelFormatter $giftSelectionPanelFormatter,
    ): Response {
        if (!$this->getUser() instanceof User) {
            return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
        }

        $user = $entityManager->getRepository(User::class)->find($id);
        $giftRequests = $user->getGiftRequests()->toArray(); 
        
        $giftSelectionPanelConfig = new GiftSelectionPanelConfig(
            gifts: $giftSelectionPanelFormatter->fromEntityList($giftRequests)
        );

        return $this->render('user/view.html.twig', [
            'giftSelectionPanelConfig' => $giftSelectionPanelConfig,
        ]);
    }
}