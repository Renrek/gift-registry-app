<?php declare(strict_types=1);

namespace App\Controller\GiftRequest\DTOs;

class GiftRequestFormatter
{

    public function fromModels(array $giftRequests): array
    {
        $requests = [];
        foreach ($giftRequests as $request) {
            $requests[] = new GiftRequestListItemDTO(
                id: $request->getId(),
                name: $request->getName(),
                description: $request->getDescription(),
            );
        }

        return $requests;
    }
}