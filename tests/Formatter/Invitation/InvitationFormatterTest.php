<?php declare(strict_types=1);

namespace App\Tests\Formatter\Invitation;

use App\Entity\Invitation;
use App\Formatter\Invitation\InvitationFormatter;
use PHPUnit\Framework\TestCase;

class InvitationFormatterTest extends TestCase
{
    public function testFromEntityThrowsWhenRequiredFieldsMissing(): void
    {
        $formatter = new InvitationFormatter();

        $invitation = $this->createMock(Invitation::class);
        $invitation->method('getId')->willReturn(null);
        $invitation->method('getEmail')->willReturn('invitee@example.com');
        $invitation->method('getInvitationCode')->willReturn('abc123');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Invitation must have an ID, email and code.');

        $formatter->fromEntity($invitation);
    }

    public function testFromEntityMapsInvitationToDto(): void
    {
        $formatter = new InvitationFormatter();

        $invitation = $this->createMock(Invitation::class);
        $invitation->method('getId')->willReturn(9);
        $invitation->method('getEmail')->willReturn('invitee@example.com');
        $invitation->method('getInvitationCode')->willReturn('abc123');
        $invitation->method('isUsed')->willReturn(true);

        $dto = $formatter->fromEntity($invitation);

        $this->assertSame(9, $dto->id);
        $this->assertSame('invitee@example.com', $dto->email);
        $this->assertTrue($dto->isUsed);
        $this->assertSame('abc123', $dto->code);
    }

    public function testForInvitationPanelMapsList(): void
    {
        $formatter = new InvitationFormatter();

        $first = $this->createMock(Invitation::class);
        $first->method('getId')->willReturn(1);
        $first->method('getEmail')->willReturn('first@example.com');
        $first->method('getInvitationCode')->willReturn('first-code');
        $first->method('isUsed')->willReturn(false);

        $second = $this->createMock(Invitation::class);
        $second->method('getId')->willReturn(2);
        $second->method('getEmail')->willReturn('second@example.com');
        $second->method('getInvitationCode')->willReturn('second-code');
        $second->method('isUsed')->willReturn(true);

        $result = $formatter->forInvitationPanel([$first, $second]);

        $this->assertCount(2, $result);
        $this->assertSame('first@example.com', $result[0]->email);
        $this->assertSame('second@example.com', $result[1]->email);
    }
}
