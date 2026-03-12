<?php declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\FileUploadService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadServiceTest extends TestCase
{
    private string $targetDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetDirectory = sys_get_temp_dir() . '/file-upload-service-test-' . uniqid('', true);
        mkdir($this->targetDirectory, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->targetDirectory);
        parent::tearDown();
    }

    public function testSaveBase64ImageWithDataUrlCreatesImageInSubfolder(): void
    {
        $service = new FileUploadService($this->targetDirectory);
        $base64 = 'data:image/png;base64,' . base64_encode('fake-image-bytes');

        $relativePath = $service->saveBase64Image($base64, 'gift-images');

        $this->assertNotNull($relativePath);
        $this->assertStringStartsWith('gift-images/', $relativePath);
        $this->assertStringEndsWith('.png', $relativePath);
        $this->assertFileExists($this->targetDirectory . DIRECTORY_SEPARATOR . $relativePath);
    }

    public function testSaveBase64ImageWithoutDataUrlDefaultsToJpg(): void
    {
        $service = new FileUploadService($this->targetDirectory);
        $plainBase64 = base64_encode('plain-image-data');

        $relativePath = $service->saveBase64Image($plainBase64);

        $this->assertNotNull($relativePath);
        $this->assertStringStartsWith('gift-images/', $relativePath);
        $this->assertStringEndsWith('.jpg', $relativePath);
        $this->assertFileExists($this->targetDirectory . DIRECTORY_SEPARATOR . $relativePath);
    }

    public function testUploadStoresUploadedFileInOptionalSubfolder(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('guessExtension')->willReturn('jpg');
        $uploadedFile->expects($this->once())
            ->method('move')
            ->with(
                $this->stringContains($this->targetDirectory . DIRECTORY_SEPARATOR . 'gift-images'),
                $this->matchesRegularExpression('/^[a-z0-9]+\.jpg$/i')
            );

        $service = new FileUploadService($this->targetDirectory);
        $relativePath = $service->upload($uploadedFile, 'gift-images');

        $this->assertStringStartsWith('gift-images/', $relativePath);
        $this->assertStringEndsWith('.jpg', $relativePath);
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
                continue;
            }

            @unlink($path);
        }

        @rmdir($directory);
    }
}
