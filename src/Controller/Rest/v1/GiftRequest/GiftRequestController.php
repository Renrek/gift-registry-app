<?php declare(strict_types=1);

namespace App\Controller\Rest\v1\GiftRequest;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\GiftRequest;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/gift-requests')]
class GiftRequestController extends AbstractController
{

    #[Route(path: '/add', methods: ['POST'], name: 'api_v1_add_gift_request')]
    public function handleAddGiftRequest(
        Request $request,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter,
    ): Response {
        
        $giftData = $giftFormatter->newGiftRequest(($request));

        $newGiftRequest = new GiftRequest();
        $newGiftRequest->setName((string) $giftData->name);
        $newGiftRequest->setDescription((string) $giftData->description);
        $newGiftRequest->setOwner($user);
        $newGiftRequest->setFulfilled(false);

        $entityManager->persist($newGiftRequest);
        $entityManager->flush();

        return $this->json($giftFormatter->fromEntity($newGiftRequest), Response::HTTP_CREATED);
    }

    #[Route(
        path: '/{id}/edit', 
        methods: 'POST', 
        name: 'api_v1_update_gift_request'
    )]
    public function handleEditGiftRequest(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->findOrFail($id);
        
        $giftRequest->setName((string) $request->request->get('name'));
        $giftRequest->setDescription((string) $request->request->get('description'));

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Gift Request Updated'
        ], Response::HTTP_OK);
    }

    #[Route(path: '/{id}/delete', methods: 'DELETE', name: 'api_v1_delete_gift_request')]
    public function handleDeleteGiftRequest(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);

        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }

        $entityManager->remove($giftRequest);
        $entityManager->flush();

        return $this->json(['message' => 'Gift Request Deleted'], Response::HTTP_OK);
    }
}