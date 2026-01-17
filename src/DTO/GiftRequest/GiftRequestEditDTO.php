<?php declare(strict_types=1);

namespace App\DTO\GiftRequest;

use App\Attributes\DTO;

#[DTO]
class GiftRequestEditDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public ?string $imageBase64 = null,
        public bool $removeImage = false,
        public ?string $imagePath = null,
    ) {}
}