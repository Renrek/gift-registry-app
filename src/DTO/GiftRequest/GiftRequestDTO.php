<?php declare(strict_types=1);

namespace App\DTO\GiftRequest;

use App\Attributes\DTO;

#[DTO]
class GiftRequestDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $editPath,
        public string $deletePath,
    ) {}
}