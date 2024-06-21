<?php declare(strict_types=1);

namespace App\Controller\GiftRequest\DTOs;

use App\Controller\GiftRequest\DTOs\GiftRegistrationRequestDTO;
use Symfony\Component\HttpFoundation\Request;

class GiftRegistrationFormatter
{
    public function fromRequest(Request $request): GiftRegistrationRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        return new GiftRegistrationRequestDTO(
            name: $payload->name,
            description: $payload->description,
        );
    }
}