<?php declare(strict_types=1);

namespace App\Formatter\Registration;

use App\DTO\Registration\RegistrationRequestDTO;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormatter
{
    public function fromRequest(Request $request): RegistrationRequestDTO
    {
        $payload = json_decode($request->getContent(), false);
        
        return new RegistrationRequestDTO(
            email: $payload->email,
            password: $payload->password,
            invitationCode: $payload->invitationCode,
        );
    }
}