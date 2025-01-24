<?php declare(strict_types=1);

namespace App\Controller\GiftRequest\DTOs;

use App\Entity\GiftRequest;
use Symfony\Component\HttpFoundation\Request;

class GiftRequestFormatter
{

    /**
     * Converts a GiftRequest model to a GiftRequestListItemDTO.
     */
    public function fromModel(GiftRequest $giftRequest): GiftRequestListItemDTO
    {
        return new GiftRequestListItemDTO(
            id: $giftRequest->getId(),
            name: $giftRequest->getName(),
            description: $giftRequest->getDescription(),
            editPath: '/gift-request/edit/' . $giftRequest->getId(),
            deletePath: '/gift-request/delete/' . $giftRequest->getId(),
        );
    }

    /**
     * Converts a list of GiftRequest models to an array.
     *
     * @param GiftRequest[] $giftRequests An array of GiftRequest models.
     * @return GiftRequestListItemDTO[]
     */
    public function fromModelList(array $giftRequests): array
    {
        $requests = [];
        foreach ($giftRequests as $request) {
            $requests[] = $this->fromModel($request);
        }

        return $requests;
    }

    public function newGiftRequest(Request $request): NewGiftRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        return new NewGiftRequestDTO(
            name: $payload->name,
            description: $payload->description,
        );
    }

    public function editGiftRequest(Request $request): GiftRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        return new GiftRequestDTO(
            id: $payload->id,
            name: $payload->name,
            description: $payload->description,
        );
    }
}