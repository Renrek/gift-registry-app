<?php declare(strict_types=1);

namespace App\Controller\Web\GiftRequest\DTOs;

use App\Attributes\DTO;

#[DTO]
class NewGiftRequestDTO
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}
}