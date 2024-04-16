<?php declare(strict_types=1);

namespace App\Dto\Formatter;

use App\Dto\Request\UserRegistrationRequestDto;
use Symfony\Component\HttpFoundation\Request;

class UserRegistrationFormatter
{
    public function fromRequest(Request $request): UserRegistrationRequestDto
    {
        $payload = json_decode($request->getContent(), false);
        
        return new UserRegistrationRequestDto(
            email: $payload->email,
            password: $payload->password
        );
    }
}