<?php declare(strict_types=1);

namespace App\Controller\GiftRequest\DTOs;

class GiftRequestListItemDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
    ) {}
}