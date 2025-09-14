<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\GiftRequest\GiftRequestEditDTO;
use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\GiftRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class GiftRequestService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

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
        if ($data->name) {
            $giftRequest->setName((string) $data->name);
        }
        if ($data->description) {
            $giftRequest->setDescription((string) $data->description);
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
        $this->entityManager->remove($giftRequest);
        $this->entityManager->flush();
    }
}
