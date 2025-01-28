<?php

namespace App\Tests\Service;

use App\Service\UuidService;
use PHPUnit\Framework\TestCase;

class UuidServiceTest extends TestCase
{
    public function testGenerateV4UUID(): void
    {
        $uuidService = new UuidService();
        $uuid = $uuidService->generateV4UUID();

        // Check if the UUID is a valid V4 UUID
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }

    public function testGenerateV1UUID(): void
    {
        $uuidService = new UuidService();
        $uuid = $uuidService->generateV1UUID();

        // Check if the UUID is a valid V1 UUID
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid
        );
    }
}