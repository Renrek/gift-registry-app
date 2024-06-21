<?php declare(strict_types=1);

namespace App\Controller\Auth\DTOs;

use App\Controller\Auth\DTOs\UserRegistrationRequestDTO;
use Symfony\Component\HttpFoundation\Request;

class UserRegistrationFormatter
{
    public function fromRequest(Request $request): UserRegistrationRequestDTO
    {
        $payload = json_decode($request->getContent(), false);
        
        return new UserRegistrationRequestDTO(
            email: $payload->email,
            password: $payload->password
        );
    }
}