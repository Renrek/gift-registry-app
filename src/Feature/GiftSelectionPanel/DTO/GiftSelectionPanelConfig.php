<?php declare(strict_types=1);

namespace App\Feature\GiftSelectionPanel\DTO;

use App\Attributes\DTO;
use App\Attributes\ArrayOf;

#[DTO]
class GiftSelectionPanelConfig
{
    /**
     * @param GiftSelectionPanelItemDTO[] $gifts
     */
    public function __construct(
        #[ArrayOf(GiftSelectionPanelItemDTO::class)]
        public array $gifts,
    ) {}
}