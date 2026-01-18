<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\GiftRequest\GiftRequestEditDTO;
use App\Service\FileUploadService;
use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\GiftRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class GiftRequestService
{
    private FileUploadService $fileUploadService;

    public function __construct(private EntityManagerInterface $entityManager, FileUploadService $fileUploadService) {
        $this->fileUploadService = $fileUploadService;
    }

    public function getGiftRequestById(int $id): GiftRequest
    {
        $giftRequest = $this->entityManager->getRepository(GiftRequest::class)->find($id);
        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }
        return $giftRequest;
    }

    public function createGiftRequest(NewGiftRequestDTO $giftData, User $user): GiftRequest
    {
        $giftRequest = new GiftRequest();
        $giftRequest->setName((string) $giftData->name);
        $giftRequest->setDescription((string) $giftData->description);
        $giftRequest->setOwner($user);
        $giftRequest->setFulfilled(false);
        if (!empty($giftData->imagePath)) {
            $giftRequest->setImagePath($giftData->imagePath);
        }
        $this->entityManager->persist($giftRequest);
        $this->entityManager->flush();
        return $giftRequest;
    }

    public function updateGiftRequest(int $id, GiftRequestEditDTO $data): GiftRequest
    {
        $giftRequest = $this->entityManager->getRepository(GiftRequest::class)->find($id);
        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }
        $currentImage = $giftRequest->getImagePath();

        if ($data->name) {
            $giftRequest->setName((string) $data->name);
        }
        if ($data->description) {
            $giftRequest->setDescription((string) $data->description);
        }

        // Handle image removal
        if (!empty($data->removeImage)) {
            if ($currentImage) {
                $fullPath = $this->fileUploadService->getTargetDirectory() . DIRECTORY_SEPARATOR . $currentImage;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
                $giftRequest->setImagePath(null);
            }
        }

        // Handle new image path (already saved by controller)
        if (!empty($data->imagePath)) {
            // remove old file if different
            if ($currentImage && $currentImage !== $data->imagePath) {
                $oldFull = $this->fileUploadService->getTargetDirectory() . DIRECTORY_SEPARATOR . $currentImage;
                if (file_exists($oldFull)) {
                    @unlink($oldFull);
                }
            }
            $giftRequest->setImagePath($data->imagePath);
        }
        $this->entityManager->flush();
        return $giftRequest;
    }

    public function deleteGiftRequest(int $id): void
    {
        $giftRequest = $this->entityManager->getRepository(GiftRequest::class)->find($id);
        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }

        // Delete associated image file if it exists
        if ($giftRequest->getImagePath()) {
            $fullPath = $this->fileUploadService->getTargetDirectory() . DIRECTORY_SEPARATOR . $giftRequest->getImagePath();
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $this->entityManager->remove($giftRequest);
        $this->entityManager->flush();
    }
}
