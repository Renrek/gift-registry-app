<?php declare(strict_types=1);

namespace App\Controller\Web\GiftRequest;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\GiftRequest;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/gift-requests')]
class GiftRequestController extends AbstractController
{

    #[Route(path:'', methods: 'GET', name: 'gift_requests')]
    public function index(
        #[CurrentUser] ?User $user,
        GiftRequestFormatter $giftRequestFormatter
    ): Response {
        
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $giftRequests = $user->getGiftRequests()->toArray();
        
        return $this->render('gift-request/index.html.twig', [
            'giftRequests' => $giftRequestFormatter->fromEntityList($giftRequests),
            'addGiftRequestURL' => $this->generateUrl('api_v1_add_gift_request'),
        ]);
    }

}