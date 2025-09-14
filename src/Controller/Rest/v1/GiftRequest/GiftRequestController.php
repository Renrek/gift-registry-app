<?php declare(strict_types=1);

namespace App\Controller\Rest\v1\GiftRequest;

use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use App\Service\GiftRequestService;
use App\Service\FileUploadService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        GiftRequestFormatter $giftFormatter,
        GiftRequestService $giftRequestService,
        FileUploadService $fileUploadService,
    ): Response {
        if (!$user) {
            throw $this->createAccessDeniedException('User must be logged in to create a gift request.');
        }


        $giftData = $giftFormatter->newGiftRequest($request);

        if (!empty($giftData->imageBase64)) {
            $imagePath = $fileUploadService->saveBase64Image($giftData->imageBase64, 'gift-images');
            $giftData->imagePath = $imagePath;
        }

        $newGiftRequest = $giftRequestService->createGiftRequest($giftData, $user);
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
        GiftRequestService $giftRequestService,
        GiftRequestFormatter $giftFormatter,
    ): Response {
        $data = $giftFormatter->editDtoFromRequest($request);
        $giftRequestService->updateGiftRequest($id, $data);
        return $this->json([
            'success' => true,
            'message' => 'Gift Request Updated'
        ], Response::HTTP_OK);
    }

    #[Route(path: '/{id}/delete', methods: 'DELETE', name: 'api_v1_delete_gift_request')]
    public function handleDeleteGiftRequest(
        int $id,
        GiftRequestService $giftRequestService
    ): Response {
        $giftRequestService->deleteGiftRequest($id);
        return $this->json(['message' => 'Gift Request Deleted'], Response::HTTP_OK);
    }
}