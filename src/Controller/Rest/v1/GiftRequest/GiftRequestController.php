<?php declare(strict_types=1);

namespace App\Controller\Rest\v1\GiftRequest;

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


        $imagePath = null;
        $isJson = str_contains($request->headers->get('content-type', ''), 'application/json');
        if ($isJson) {
            $payload = json_decode($request->getContent(), false);
            // Handle base64 image if present
            if (!empty($payload->imageBase64)) {
                $matches = [];
                // Accept data URLs or plain base64
                if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $payload->imageBase64, $matches)) {
                    $ext = $matches[1];
                    $base64 = $matches[2];
                } else {
                    $ext = 'jpg';
                    $base64 = $payload->imageBase64;
                }
                $data = base64_decode($base64);
                $filename = uniqid('gift_', true) . '.' . $ext;
                $subfolder = 'gift-images';
                $uploadDir = $fileUploadService->getTargetDirectory() . DIRECTORY_SEPARATOR . $subfolder;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $filename, $data);
                $imagePath = $subfolder . '/' . $filename;
            }
            $giftData = new \App\DTO\GiftRequest\NewGiftRequestDTO(
                $payload->name ?? '',
                $payload->description ?? '',
                $imagePath
            );
        } else {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $request->files->get('image');
            if ($imageFile instanceof UploadedFile) {
                $imagePath = $fileUploadService->upload($imageFile, 'gift-images');
            }
            $giftData = new \App\DTO\GiftRequest\NewGiftRequestDTO(
                $request->request->get('name', ''),
                $request->request->get('description', ''),
                $imagePath
            );
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