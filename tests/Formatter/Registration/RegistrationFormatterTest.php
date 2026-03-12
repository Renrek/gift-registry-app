<?php declare(strict_types=1);

namespace App\Tests\Formatter\Registration;

use App\Formatter\Registration\RegistrationFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RegistrationFormatterTest extends TestCase
{
    public function testFromRequestThrowsWhenJsonIsInvalid(): void
    {
        $formatter = new RegistrationFormatter();
        $request = new Request(content: '{invalid-json');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON payload');

        $formatter->fromRequest($request);
    }

    public function testFromRequestThrowsWhenFieldsAreMissing(): void
    {
        $formatter = new RegistrationFormatter();
        $request = new Request(content: json_encode([
            'email' => 'user@example.com',
            'password' => 'secret',
        ]));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required fields in payload');

        $formatter->fromRequest($request);
    }

    public function testFromRequestReturnsDtoForValidPayload(): void
    {
        $formatter = new RegistrationFormatter();
        $request = new Request(content: json_encode([
            'email' => 'user@example.com',
            'password' => 'secret',
            'invitationCode' => 'inv-123',
        ]));

        $dto = $formatter->fromRequest($request);

        $this->assertSame('user@example.com', $dto->email);
        $this->assertSame('secret', $dto->password);
        $this->assertSame('inv-123', $dto->invitationCode);
    }
}
