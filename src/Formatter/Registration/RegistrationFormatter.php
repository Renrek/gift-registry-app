<?php declare(strict_types=1);

namespace App\Formatter\Registration;

use App\DTO\Registration\RegistrationRequestDTO;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormatter
{
    public function fromRequest(Request $request): RegistrationRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        if (json_last_error() !== JSON_ERROR_NONE || !is_object($payload)) {
            throw new \InvalidArgumentException('Invalid JSON payload');
        }

        if (!isset($payload->email, $payload->password, $payload->invitationCode)) {
            throw new \InvalidArgumentException('Missing required fields in payload');
        }

        return new RegistrationRequestDTO(
            email: $payload->email,
            password: $payload->password,
            invitationCode: $payload->invitationCode,
        );
    }
}