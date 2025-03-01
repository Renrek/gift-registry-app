<?php declare(strict_types=1);

namespace App\Feature\GiftSelectionPanel\DTO;

use App\Attributes\DTO;

#[DTO]
class GiftSelectionPanelItemDTO
{
    public function __construct(
        public int $giftId,
        public string $name,
        public string $description,
        public string $claimUrl,
    ) {}
}