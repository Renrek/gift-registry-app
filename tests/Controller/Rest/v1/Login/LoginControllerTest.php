<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest\v1\Login;

use App\Controller\Rest\v1\Login\LoginController;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends TestCase
{
    public function testIndexReturnsUnauthorizedWhenUserIsMissing(): void
    {
        $controller = $this->createController();

        $response = $controller->index(null);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertSame('{"message":"missing credentials"}', $response->getContent());
    }

    public function testIndexReturnsUserIdentifierWhenAuthenticated(): void
    {
        $controller = $this->createController();

        $user = $this->createMock(User::class);
        $user->method('getUserIdentifier')->willReturn('user@example.com');

        $response = $controller->index($user);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('{"user":"user@example.com"}', $response->getContent());
    }

    private function createController(): LoginController
    {
        return new class extends LoginController {
            public function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
            {
                return new JsonResponse($data, $status, $headers);
            }
        };
    }
}
