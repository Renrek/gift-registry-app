<?php declare(strict_types=1);

namespace App\DTO\Registration;

class RegistrationRequestDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $invitationCode,
    ) {}
}