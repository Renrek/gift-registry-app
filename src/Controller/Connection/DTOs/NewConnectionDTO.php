<?php declare(strict_types=1);

namespace App\Controller\Connection\DTOs;

use App\Attributes\DTO;

#[DTO]
class NewConnectionDTO
{
    public function __construct(
        public int $userId,
        public int $connectedUserId,
    ) {}
}
