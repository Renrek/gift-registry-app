<?php declare(strict_types=1);

namespace App\Controller\Web\User;

use App\Entity\User;
use App\Feature\GiftSelectionPanel\DTO\GiftSelectionPanelConfig;
use App\Feature\GiftSelectionPanel\Formatter\GiftSelectionPanelFormatter;
use App\Formatter\User\UserFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/users')]
class UserController extends AbstractController
{
    #[Route(path: '/{id}/view', methods: ['GET'], name: 'get_user_view')]
    public function handleGetUserView(
        int $id,
        EntityManagerInterface $entityManager,
        GiftSelectionPanelFormatter $giftSelectionPanelFormatter,
        UserFormatter $userFormatter,
    ): Response {
        if (!$this->getUser() instanceof User) {
            return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user === null) {
            throw new \LogicException('User must not be null.');
        }
        
        $giftRequests = $user->getGiftRequests()->toArray(); 
        
        $giftSelectionPanelConfig = new GiftSelectionPanelConfig(
            gifts: $giftSelectionPanelFormatter->fromEntityList($giftRequests)
        );

        return $this->render('user/view.html.twig', [
            'user' => $userFormatter->fromEntity($user),
            'giftSelectionPanelConfig' => $giftSelectionPanelConfig,
        ]);
    }
}