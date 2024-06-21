<?php declare(strict_types=1);

namespace App\Controller\GiftRequest\DTOs;

class GiftRegistrationRequestDTO
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}
}