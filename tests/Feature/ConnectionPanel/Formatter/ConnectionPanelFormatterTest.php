<?php declare(strict_types=1);

namespace App\Tests\Feature\ConnectionPanel\Formatter;

use App\Entity\Connection;
use App\Entity\User;
use App\Feature\ConnectionPanel\Enum\ConfirmStatus;
use App\Feature\ConnectionPanel\Formatter\ConnectionPanelFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConnectionPanelFormatterTest extends TestCase
{
    public function testFromEntityThrowsWhenUserIsNotLoggedIn(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $formatter = new ConnectionPanelFormatter($urlGenerator, $security);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The user is not logged in or is not an instance of User.');

        $formatter->fromEntity($this->createMock(Connection::class));
    }

    public function testFromEntityThrowsWhenConnectionHasMissingUsers(): void
    {
        $currentUser = $this->createMock(User::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $connection = $this->createMock(Connection::class);
        $connection->method('getUser')->willReturn(null);
        $connection->method('getConnectedUser')->willReturn(null);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $formatter = new ConnectionPanelFormatter($urlGenerator, $security);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The connection does not have a user or connected user.');

        $formatter->fromEntity($connection);
    }

    public function testFromEntityUsesPendingStatusForUnconfirmedInitiator(): void
    {
        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(10);

        $connectedUser = $this->createMock(User::class);
        $connectedUser->method('getId')->willReturn(20);
        $connectedUser->method('getEmail')->willReturn('connected@example.com');

        $connection = $this->createMock(Connection::class);
        $connection->method('getId')->willReturn(50);
        $connection->method('getUser')->willReturn($currentUser);
        $connection->method('getConnectedUser')->willReturn($connectedUser);
        $connection->method('isConfirmed')->willReturn(false);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            function (string $route, array $parameters): string {
                return match ($route) {
                    'api_v1_confirm_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/confirm',
                    'api_v1_delete_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/delete',
                    'get_user_view' => '/users/' . $parameters['id'] . '/view',
                    default => throw new \LogicException('Unexpected route'),
                };
            }
        );

        $formatter = new ConnectionPanelFormatter($urlGenerator, $security);
        $dto = $formatter->fromEntity($connection);

        $this->assertSame(50, $dto->id);
        $this->assertSame(20, $dto->userId);
        $this->assertSame('connected@example.com', $dto->email);
        $this->assertSame(ConfirmStatus::PENDING, $dto->status);
        $this->assertSame('/api/v1/connections/50/confirm', $dto->confirmUrl);
        $this->assertSame('/api/v1/connections/50/delete', $dto->deleteUrl);
        $this->assertSame('/users/20/view', $dto->viewUrl);
    }

    public function testFromEntityUsesNotConfirmedForRecipientWhenUnconfirmed(): void
    {
        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(20);

        $initiator = $this->createMock(User::class);
        $initiator->method('getId')->willReturn(10);
        $initiator->method('getEmail')->willReturn('initiator@example.com');

        $connection = $this->createMock(Connection::class);
        $connection->method('getId')->willReturn(60);
        $connection->method('getUser')->willReturn($initiator);
        $connection->method('getConnectedUser')->willReturn($currentUser);
        $connection->method('isConfirmed')->willReturn(false);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            function (string $route, array $parameters): string {
                return match ($route) {
                    'api_v1_confirm_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/confirm',
                    'api_v1_delete_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/delete',
                    'get_user_view' => '/users/' . $parameters['id'] . '/view',
                    default => throw new \LogicException('Unexpected route'),
                };
            }
        );

        $formatter = new ConnectionPanelFormatter($urlGenerator, $security);
        $dto = $formatter->fromEntity($connection);

        $this->assertSame(10, $dto->userId);
        $this->assertSame('initiator@example.com', $dto->email);
        $this->assertSame(ConfirmStatus::NOT_CONFIRMED, $dto->status);
    }

    public function testForContactsPanelMapsAllConnections(): void
    {
        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(100);

        $otherA = $this->createMock(User::class);
        $otherA->method('getId')->willReturn(101);
        $otherA->method('getEmail')->willReturn('a@example.com');

        $otherB = $this->createMock(User::class);
        $otherB->method('getId')->willReturn(102);
        $otherB->method('getEmail')->willReturn('b@example.com');

        $first = $this->createMock(Connection::class);
        $first->method('getId')->willReturn(1);
        $first->method('getUser')->willReturn($currentUser);
        $first->method('getConnectedUser')->willReturn($otherA);
        $first->method('isConfirmed')->willReturn(true);

        $second = $this->createMock(Connection::class);
        $second->method('getId')->willReturn(2);
        $second->method('getUser')->willReturn($currentUser);
        $second->method('getConnectedUser')->willReturn($otherB);
        $second->method('isConfirmed')->willReturn(true);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($currentUser);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturnCallback(
            function (string $route, array $parameters): string {
                return match ($route) {
                    'api_v1_confirm_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/confirm',
                    'api_v1_delete_connection' => '/api/v1/connections/' . $parameters['connectionId'] . '/delete',
                    'get_user_view' => '/users/' . $parameters['id'] . '/view',
                    default => throw new \LogicException('Unexpected route'),
                };
            }
        );

        $formatter = new ConnectionPanelFormatter($urlGenerator, $security);
        $items = $formatter->forContactsPanel([$first, $second]);

        $this->assertCount(2, $items);
        $this->assertSame(101, $items[0]->userId);
        $this->assertSame(102, $items[1]->userId);
        $this->assertSame(ConfirmStatus::CONFIRMED, $items[0]->status);
        $this->assertSame(ConfirmStatus::CONFIRMED, $items[1]->status);
    }
}
