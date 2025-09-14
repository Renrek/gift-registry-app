<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\GiftRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class GiftRequestService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function createGiftRequest(object $giftData, User $user): GiftRequest
    {
        $giftRequest = new GiftRequest();
        $giftRequest->setName((string) $giftData->name);
        $giftRequest->setDescription((string) $giftData->description);
        $giftRequest->setOwner($user);
        $giftRequest->setFulfilled(false);
        $this->entityManager->persist($giftRequest);
        $this->entityManager->flush();
        return $giftRequest;
    }

    public function updateGiftRequest(int $id, array $data): GiftRequest
    {
        $giftRequest = $this->entityManager->getRepository(GiftRequest::class)->find($id);
        if (!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }
        if (isset($data['name'])) {
            $giftRequest->setName((string) $data['name']);
        }
        if (isset($data['description'])) {
            $giftRequest->setDescription((string) $data['description']);
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
