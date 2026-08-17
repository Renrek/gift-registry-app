<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest\v1\Invitation;

use App\Controller\Rest\v1\Invitation\InvitationController;
use App\Entity\User;
use App\Service\InvitationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationControllerTest extends TestCase
{
    public function testCreateReturnsUnauthorizedWhenUserIsMissing(): void
    {
        $controller = $this->createController(null);
        $request = new Request(content: json_encode(['email' => 'invitee@example.com'], JSON_THROW_ON_ERROR));

        $service = $this->createMock(InvitationService::class);

        $response = $controller->create($request, $service);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertSame('User not authenticated', $response->getContent());
    }

    public function testCreateReturnsBadRequestWhenEmailMissing(): void
    {
        $user = $this->createMock(User::class);
        $controller = $this->createController($user);
        $request = new Request(content: json_encode(['email' => ''], JSON_THROW_ON_ERROR));

        $service = $this->createMock(InvitationService::class);

        $response = $controller->create($request, $service);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertSame('Email is required', $response->getContent());
    }

    public function testCreateReturnsCreatedWhenInvitationIsMade(): void
    {
        $user = $this->createMock(User::class);
        $controller = $this->createController($user);
        $request = new Request(content: json_encode(['email' => 'invitee@example.com'], JSON_THROW_ON_ERROR));

        $service = $this->createMock(InvitationService::class);
        $service->expects($this->once())
            ->method('createInvitation')
            ->with('invitee@example.com');

        $response = $controller->create($request, $service);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertSame('Invitation created successfully', $response->getContent());
    }

    private function createController(?User $user): InvitationController
    {
        return new class($user) extends InvitationController {
            public function __construct(private ?User $currentUser)
            {
            }

            public function getUser(): ?User
            {
                return $this->currentUser;
            }
        };
    }
}
