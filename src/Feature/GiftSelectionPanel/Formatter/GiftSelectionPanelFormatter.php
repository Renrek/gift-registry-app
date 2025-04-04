<?php declare(strict_types=1);

namespace App\Feature\GiftSelectionPanel\Formatter;

use App\Entity\GiftRequest;
use App\Feature\GiftSelectionPanel\DTO\GiftSelectionPanelItemDTO;

class GiftSelectionPanelFormatter
{
    /** 
     * @param GiftRequest[] $gifts
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

    public function fromEntity(GiftRequest $gift): GiftSelectionPanelItemDTO
    {
        if (!$gift->getId()) {
            throw new \InvalidArgumentException('No Gift ID provided');
        }

        return new GiftSelectionPanelItemDTO(
            giftId: $gift->getId(),
            name: $gift->getName() ?? '',
            description: $gift->getDescription() ?? '',
            claimUrl: '',
        );
    }
}