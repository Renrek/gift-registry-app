<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\GiftRequest\GiftRequestEditDTO;
use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\GiftRequest;
use App\Entity\User;
use App\Service\FileUploadService;
use App\Service\GiftRequestService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;

class GiftRequestServiceTest extends TestCase
{
    public function testGetGiftRequestByIdThrowsWhenNotFound(): void
    {
        $missingGiftRequestId = 123;

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($missingGiftRequestId)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);

        $fileUploadService = $this->createMock(FileUploadService::class);
        $service = new GiftRequestService($entityManager, $fileUploadService);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Gift Request not found');

        $service->getGiftRequestById($missingGiftRequestId);
    }

    public function testCreateGiftRequestPersistsEntityWithImagePath(): void
    {
        $giftData = new NewGiftRequestDTO(
            name: 'Mixer',
            description: 'Kitchen mixer',
            imagePath: 'gift-images/mixer.jpg',
            imageBase64: null,
        );

        $user = new User();
        $user->setEmail('owner@example.com');

        $repository = $this->createMock(EntityRepository::class);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (GiftRequest $giftRequest) use ($user): bool {
                return $giftRequest->getName() === 'Mixer'
                    && $giftRequest->getDescription() === 'Kitchen mixer'
                    && $giftRequest->getOwner() === $user
                    && $giftRequest->getFulfilled() === false
                    && $giftRequest->getImagePath() === 'gift-images/mixer.jpg';
            }));
        $entityManager->expects($this->once())->method('flush');

        $fileUploadService = $this->createMock(FileUploadService::class);
        $service = new GiftRequestService($entityManager, $fileUploadService);

        $result = $service->createGiftRequest($giftData, $user);

        $this->assertInstanceOf(GiftRequest::class, $result);
        $this->assertSame('Mixer', $result->getName());
        $this->assertSame('Kitchen mixer', $result->getDescription());
        $this->assertSame('gift-images/mixer.jpg', $result->getImagePath());
    }

    public function testUpdateGiftRequestRemovesExistingImageWhenRemoveImageTrue(): void
    {
        $existingGiftRequestId = 1;

        $tempDir = sys_get_temp_dir() . '/gift-request-test-' . uniqid('', true);
        mkdir($tempDir, 0777, true);
        $existingImage = $tempDir . '/old.jpg';
        file_put_contents($existingImage, 'old-image');

        $giftRequest = new GiftRequest();
        $giftRequest->setName('Old Name');
        $giftRequest->setDescription('Old Description');
        $giftRequest->setImagePath('old.jpg');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($existingGiftRequestId)->willReturn($giftRequest);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);
        $entityManager->expects($this->once())->method('flush');

        $fileUploadService = $this->createMock(FileUploadService::class);
        $fileUploadService->method('getTargetDirectory')->willReturn($tempDir);

        $service = new GiftRequestService($entityManager, $fileUploadService);

        $data = new GiftRequestEditDTO(
            name: 'New Name',
            description: 'New Description',
            imageBase64: null,
            removeImage: true,
            imagePath: null,
        );

        $updated = $service->updateGiftRequest($existingGiftRequestId, $data);

        $this->assertSame('New Name', $updated->getName());
        $this->assertSame('New Description', $updated->getDescription());
        $this->assertNull($updated->getImagePath());
        $this->assertFileDoesNotExist($existingImage);

        @rmdir($tempDir);
    }

    public function testUpdateGiftRequestReplacesImageAndDeletesOldFile(): void
    {
        $existingGiftRequestId = 2;

        $tempDir = sys_get_temp_dir() . '/gift-request-test-' . uniqid('', true);
        mkdir($tempDir, 0777, true);
        $existingImage = $tempDir . '/old.jpg';
        file_put_contents($existingImage, 'old-image');

        $giftRequest = new GiftRequest();
        $giftRequest->setImagePath('old.jpg');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($existingGiftRequestId)->willReturn($giftRequest);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);
        $entityManager->expects($this->once())->method('flush');

        $fileUploadService = $this->createMock(FileUploadService::class);
        $fileUploadService->method('getTargetDirectory')->willReturn($tempDir);

        $service = new GiftRequestService($entityManager, $fileUploadService);

        $data = new GiftRequestEditDTO(
            name: '',
            description: '',
            imageBase64: null,
            removeImage: false,
            imagePath: 'new.jpg',
        );

        $updated = $service->updateGiftRequest($existingGiftRequestId, $data);

        $this->assertSame('new.jpg', $updated->getImagePath());
        $this->assertFileDoesNotExist($existingImage);

        @rmdir($tempDir);
    }

    public function testDeleteGiftRequestRemovesImageAndEntity(): void
    {
        $existingGiftRequestId = 3;

        $tempDir = sys_get_temp_dir() . '/gift-request-test-' . uniqid('', true);
        mkdir($tempDir, 0777, true);
        $existingImage = $tempDir . '/delete-me.jpg';
        file_put_contents($existingImage, 'old-image');

        $giftRequest = new GiftRequest();
        $giftRequest->setImagePath('delete-me.jpg');

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($existingGiftRequestId)->willReturn($giftRequest);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);
        $entityManager->expects($this->once())->method('remove')->with($giftRequest);
        $entityManager->expects($this->once())->method('flush');

        $fileUploadService = $this->createMock(FileUploadService::class);
        $fileUploadService->method('getTargetDirectory')->willReturn($tempDir);

        $service = new GiftRequestService($entityManager, $fileUploadService);
        $service->deleteGiftRequest($existingGiftRequestId);

        $this->assertFileDoesNotExist($existingImage);

        @rmdir($tempDir);
    }

    public function testDeleteGiftRequestThrowsWhenNotFound(): void
    {
        $missingGiftRequestId = 999;

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with($missingGiftRequestId)->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(GiftRequest::class)->willReturn($repository);

        $fileUploadService = $this->createMock(FileUploadService::class);
        $service = new GiftRequestService($entityManager, $fileUploadService);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Gift Request not found');

        $service->deleteGiftRequest($missingGiftRequestId);
    }
}
