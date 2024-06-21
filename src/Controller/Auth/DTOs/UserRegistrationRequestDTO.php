<?php declare(strict_types=1);

namespace App\Controller\Auth\DTOs;

class UserRegistrationRequestDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}