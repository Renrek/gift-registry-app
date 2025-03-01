<?php declare(strict_types=1);

namespace App\Feature\GiftSelectionPanel\Formatter;

use App\Entity\GiftRequest;

class GiftSelectionPanelFormatter
{
    public function fromEntityList(array $gifts): array
    {
        $giftsArray = [];
        foreach ($gifts as $gift) {
            $giftsArray[] = $this->fromEntity($gift);
        }

        return $giftsArray;
    }

    public function fromEntity(GiftRequest $gift): array
    {
        return [
            'giftId' => $gift->getId(),
            'name' => $gift->getName(),
            'description' => $gift->getDescription(),
            'claimUrl' => '',
        ];
    }
}