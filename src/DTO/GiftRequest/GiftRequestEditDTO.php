<?php declare(strict_types=1);

namespace App\DTO\GiftRequest;

use App\Attributes\DTO;

#[DTO]
class GiftRequestEditDTO
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}
}