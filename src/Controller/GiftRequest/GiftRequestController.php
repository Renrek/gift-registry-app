<?php declare(strict_types=1);

namespace App\Controller\GiftRequest;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Controller\GiftRequest\DTOs\GiftRegistrationFormatter;
use App\Controller\GiftRequest\DTOs\GiftRequestFormatter;
use App\Entity\GiftRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/gift-request')]
class GiftRequestController extends AbstractController
{

    #[Route(path:'', methods: 'GET')]
    public function index(
        #[CurrentUser] ?User $user,
        GiftRequestFormatter $giftRequestFormatter
    ): Response {
        
        $giftRequests = $user->getGiftRequests()->toArray();
        
        return $this->render('gift/request/index.html.twig', [
            'giftRequests' => $giftRequestFormatter->fromModels($giftRequests),
        ]);
    }

    #[Route(path: '/add', methods: 'POST')]
    public function handleAddGiftRequest(
        Request $request,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        GiftRegistrationFormatter $giftFormatter,
    ): Response {
        
        $giftData = $giftFormatter->fromRequest(($request));

        $newGiftRequest = new GiftRequest();
        $newGiftRequest->setName($giftData->name);
        $newGiftRequest->setDescription($giftData->description);
        $newGiftRequest->setOwner($user);
        $newGiftRequest->setFulfilled(false);

        $entityManager->persist($newGiftRequest);
        $entityManager->flush();

        return $this->json(['message' => 'Added Gift Request'], Response::HTTP_CREATED);
    }
}