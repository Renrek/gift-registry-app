<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest\v1\GiftRequest;

use App\Controller\Rest\v1\GiftRequest\GiftRequestController;
use App\DTO\GiftRequest\GiftRequestDTO;
use App\DTO\GiftRequest\GiftRequestEditDTO;
use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\GiftRequest;
use App\Entity\User;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use App\Service\FileUploadService;
use App\Service\GiftRequestService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GiftRequestControllerTest extends TestCase
{
    public function testHandleAddGiftRequestThrowsWhenUserIsMissing(): void
    {
        $controller = $this->createController();
        $request = new Request();

        $formatter = $this->createMock(GiftRequestFormatter::class);
        $service = $this->createMock(GiftRequestService::class);
        $fileUploadService = $this->createMock(FileUploadService::class);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('User must be logged in to create a gift request.');

        $controller->handleAddGiftRequest($request, null, $formatter, $service, $fileUploadService);
    }

    public function testHandleAddGiftRequestUploadsImageWhenBase64Provided(): void
    {
        $controller = $this->createController();
        $request = new Request();

        $user = $this->createMock(User::class);

        $newGiftRequestDto = new NewGiftRequestDTO(
            name: 'Air Fryer',
            description: 'Countertop fryer',
            imagePath: null,
            imageBase64: 'base64-image-data',
        );

        $createdGiftRequest = $this->createMock(GiftRequest::class);

        $formatter = $this->createMock(GiftRequestFormatter::class);
        $formatter->expects($this->once())
            ->method('newGiftRequest')
            ->with($request)
            ->willReturn($newGiftRequestDto);
        $formatter->expects($this->once())
            ->method('fromEntity')
            ->with($createdGiftRequest)
            ->willReturn(new GiftRequestDTO(1, 'Air Fryer', 'Countertop fryer', 'gift-images/air-fryer.jpg', '/edit', '/delete'));

        $fileUploadService = $this->createMock(FileUploadService::class);
        $fileUploadService->expects($this->once())
            ->method('saveBase64Image')
            ->with('base64-image-data', 'gift-images')
            ->willReturn('gift-images/air-fryer.jpg');

        $service = $this->createMock(GiftRequestService::class);
        $service->expects($this->once())
            ->method('createGiftRequest')
            ->with($this->callback(function (NewGiftRequestDTO $dto): bool {
                return $dto->imagePath === 'gift-images/air-fryer.jpg';
            }), $user)
            ->willReturn($createdGiftRequest);

        $response = $controller->handleAddGiftRequest($request, $user, $formatter, $service, $fileUploadService);

        $this->assertSame(201, $response->getStatusCode());
    }

    public function testHandleEditGiftRequestThrowsWhenUserIsMissing(): void
    {
        $controller = $this->createController();
        $request = new Request();

        $service = $this->createMock(GiftRequestService::class);
        $formatter = $this->createMock(GiftRequestFormatter::class);
        $fileUploadService = $this->createMock(FileUploadService::class);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('User must be logged in to edit a gift request.');

        $controller->handleEditGiftRequest(10, $request, null, $service, $formatter, $fileUploadService);
    }

    public function testHandleEditGiftRequestThrowsWhenUserDoesNotOwnGift(): void
    {
        $controller = $this->createController();
        $request = new Request();

        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(100);

        $owner = $this->createMock(User::class);
        $owner->method('getId')->willReturn(200);

        $giftRequest = $this->createMock(GiftRequest::class);
        $giftRequest->method('getOwner')->willReturn($owner);

        $service = $this->createMock(GiftRequestService::class);
        $service->method('getGiftRequestById')->with(10)->willReturn($giftRequest);

        $formatter = $this->createMock(GiftRequestFormatter::class);
        $fileUploadService = $this->createMock(FileUploadService::class);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionMessage('You do not have permission to edit this gift request.');

        $controller->handleEditGiftRequest(10, $request, $currentUser, $service, $formatter, $fileUploadService);
    }

    public function testHandleEditGiftRequestUploadsNewImageAndUpdatesGift(): void
    {
        $controller = $this->createController();
        $request = new Request();

        $currentUser = $this->createMock(User::class);
        $currentUser->method('getId')->willReturn(100);

        $owner = $this->createMock(User::class);
        $owner->method('getId')->willReturn(100);

        $giftRequest = $this->createMock(GiftRequest::class);
        $giftRequest->method('getOwner')->willReturn($owner);

        $editDto = new GiftRequestEditDTO(
            name: 'Updated Gift',
            description: 'Updated Description',
            imageBase64: 'new-base64-image',
            removeImage: false,
            imagePath: null,
        );

        $updatedGift = $this->createMock(GiftRequest::class);

        $service = $this->createMock(GiftRequestService::class);
        $service->method('getGiftRequestById')->with(20)->willReturn($giftRequest);
        $service->expects($this->once())
            ->method('updateGiftRequest')
            ->with(20, $this->callback(function (GiftRequestEditDTO $dto): bool {
                return $dto->imagePath === 'gift-images/updated.jpg';
            }))
            ->willReturn($updatedGift);

        $formatter = $this->createMock(GiftRequestFormatter::class);
        $formatter->expects($this->once())
            ->method('editDtoFromRequest')
            ->with($request)
            ->willReturn($editDto);
        $formatter->expects($this->once())
            ->method('fromEntity')
            ->with($updatedGift)
            ->willReturn(new GiftRequestDTO(20, 'Updated Gift', 'Updated Description', 'gift-images/updated.jpg', '/edit', '/delete'));

        $fileUploadService = $this->createMock(FileUploadService::class);
        $fileUploadService->expects($this->once())
            ->method('saveBase64Image')
            ->with('new-base64-image', 'gift-images')
            ->willReturn('gift-images/updated.jpg');

        $response = $controller->handleEditGiftRequest(20, $request, $currentUser, $service, $formatter, $fileUploadService);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testHandleDeleteGiftRequestCallsServiceAndReturnsOk(): void
    {
        $controller = $this->createController();

        $service = $this->createMock(GiftRequestService::class);
        $service->expects($this->once())->method('deleteGiftRequest')->with(30);

        $response = $controller->handleDeleteGiftRequest(30, $service);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"message":"Gift Request Deleted"}', $response->getContent());
    }

    private function createController(): GiftRequestController
    {
        return new class extends GiftRequestController {
            public function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
            {
                return new JsonResponse($data, $status, $headers);
            }
        };
    }
}
