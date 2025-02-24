<?php declare(strict_types=1);

namespace App\DTO\User;

use App\Attributes\DTO;

#[DTO]
class UserDTO
{
    public function __construct(
        public int $id,
        public string $email,
    ) {}
}