<?php declare(strict_types=1);

namespace App\Feature\GiftSelectionPanel\Formatter;

use App\Entity\GiftRequest;
use App\Feature\GiftSelectionPanel\DTO\GiftSelectionPanelItemDTO;

class GiftSelectionPanelFormatter
{
    /**
     * Converts a list of GiftRequest entities to an array of formatted data.
     *
     * @param GiftRequest[] 
     * @return GiftSelectionPanelItemDTO[]
     */
    public function fromEntityList(array $gifts): array
    {
        $giftsArray = [];
        foreach ($gifts as $gift) {
            $giftsArray[] = $this->fromEntity($gift);
        }

        return $giftsArray;
    }

    /**
     * Converts a single GiftRequest entity to a formatted array.
     *
     * @param GiftRequest 
     */
    public function fromEntity(GiftRequest $gift): GiftSelectionPanelItemDTO
    {
        return new GiftSelectionPanelItemDTO(
            giftId: $gift->getId(),
            name: $gift->getName(),
            description: $gift->getDescription(),
            claimUrl: '',
        );
    }
}