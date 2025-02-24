<?php declare(strict_types=1);

namespace App\DTO\Invitation;

use App\Attributes\DTO;

#[DTO]
class InvitationListItemDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public bool $isUsed,
        public string $code,
    ) {}
}