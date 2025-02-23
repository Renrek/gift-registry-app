<?php declare(strict_types=1);

namespace App\Controller\Web\Auth\DTOs;

class UserRegistrationRequestDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $invitationCode,
    ) {}
}