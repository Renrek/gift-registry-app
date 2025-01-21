<?php declare(strict_types=1);

namespace App\Controller\Profile\DTOs;

class InvitationListItemDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public bool $isUsed,
        public string $code,
    ) {}
}