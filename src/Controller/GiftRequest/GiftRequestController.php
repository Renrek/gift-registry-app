<?php declare(strict_types=1);

namespace App\Controller\GiftRequest;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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
            'giftRequests' => $giftRequestFormatter->fromModelList($giftRequests),
        ]);
    }

    #[Route(path: '/add', methods: 'POST')]
    public function handleAddGiftRequest(
        Request $request,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter,
    ): Response {
        
        $giftData = $giftFormatter->newGiftRequest(($request));

        $newGiftRequest = new GiftRequest();
        $newGiftRequest->setName($giftData->name);
        $newGiftRequest->setDescription($giftData->description);
        $newGiftRequest->setOwner($user);
        $newGiftRequest->setFulfilled(false);

        $entityManager->persist($newGiftRequest);
        $entityManager->flush();

        return $this->json(['message' => 'Added Gift Request'], Response::HTTP_CREATED);
    }

    #[Route(path: '/edit/{id}', methods: 'GET')]
    public function editGiftRequest(
        int $id,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);

        return $this->render('gift/request/edit.html.twig', [
            'giftRequest' => $giftFormatter->fromModel($giftRequest),
        ]);
    }

    #[Route(path: '/edit/{id}', methods: 'POST')]
    public function handleEditGiftRequest(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);
        $giftData = $giftFormatter->editGiftRequest($request);

        $giftRequest->setName($giftData->name);
        $giftRequest->setDescription($giftData->description);

        $entityManager->flush();

        return $this->json(['message' => 'Gift Request Updated'], Response::HTTP_OK);
    }

    #[Route(path: '/delete/{id}', methods: 'POST')]
    public function handleDeleteGiftRequest(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);

        $entityManager->remove($giftRequest);
        $entityManager->flush();

        return $this->json(['message' => 'Gift Request Deleted'], Response::HTTP_OK);
    }
}