<?php declare(strict_types=1);

namespace App\Dto\Request;

class UserRegistrationRequestDto
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}