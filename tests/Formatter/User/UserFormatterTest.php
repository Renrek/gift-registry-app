<?php declare(strict_types=1);

namespace App\Tests\Formatter\User;

use App\Entity\User;
use App\Formatter\User\UserFormatter;
use PHPUnit\Framework\TestCase;

class UserFormatterTest extends TestCase
{
    public function testFromEntityThrowsWhenRequiredFieldsMissing(): void
    {
        $formatter = new UserFormatter();

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(null);
        $user->method('getEmail')->willReturn('user@example.com');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User must have an ID and email.');

        $formatter->fromEntity($user);
    }

    public function testFromEntityMapsUserToDto(): void
    {
        $formatter = new UserFormatter();

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(15);
        $user->method('getEmail')->willReturn('user@example.com');

        $dto = $formatter->fromEntity($user);

        $this->assertSame(15, $dto->id);
        $this->assertSame('user@example.com', $dto->email);
    }

    public function testFromEntityListMapsAllUsers(): void
    {
        $formatter = new UserFormatter();

        $first = $this->createMock(User::class);
        $first->method('getId')->willReturn(1);
        $first->method('getEmail')->willReturn('first@example.com');

        $second = $this->createMock(User::class);
        $second->method('getId')->willReturn(2);
        $second->method('getEmail')->willReturn('second@example.com');

        $result = $formatter->fromEntityList([$first, $second]);

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame(2, $result[1]->id);
    }
}
