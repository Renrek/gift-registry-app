<?php declare(strict_types=1);

namespace App\Tests\Formatter\GiftRequest;

use App\Entity\GiftRequest;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GiftRequestFormatterTest extends TestCase
{
    public function testFromEntityMapsFieldsAndGeneratedUrls(): void
    {
        $giftRequest = $this->createMock(GiftRequest::class);
        $giftRequest->method('getId')->willReturn(7);
        $giftRequest->method('getName')->willReturn('Desk Lamp');
        $giftRequest->method('getDescription')->willReturn('Warm light');
        $giftRequest->method('getImagePath')->willReturn('gift-images/lamp.jpg');

        $calls = [];
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->exactly(2))
            ->method('generate')
            ->willReturnCallback(function (string $route, array $parameters) use (&$calls): string {
                $calls[] = [$route, $parameters];

                if ($route === 'api_v1_update_gift_request') {
                    return '/api/v1/gift-requests/7/edit';
                }

                if ($route === 'api_v1_delete_gift_request') {
                    return '/api/v1/gift-requests/7/delete';
                }

                $this->fail('Unexpected route generated: ' . $route);
            });

        $formatter = new GiftRequestFormatter($urlGenerator);
        $dto = $formatter->fromEntity($giftRequest);

        $this->assertSame(7, $dto->id);
        $this->assertSame('Desk Lamp', $dto->name);
        $this->assertSame('Warm light', $dto->description);
        $this->assertSame('gift-images/lamp.jpg', $dto->imagePath);
        $this->assertSame('/api/v1/gift-requests/7/edit', $dto->editPath);
        $this->assertSame('/api/v1/gift-requests/7/delete', $dto->deletePath);
        $this->assertSame([
            ['api_v1_update_gift_request', ['id' => 7]],
            ['api_v1_delete_gift_request', ['id' => 7]],
        ], $calls);
    }

    public function testNewGiftRequestMapsPayloadAndDefaults(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $formatter = new GiftRequestFormatter($urlGenerator);

        $fullPayloadRequest = new Request(content: json_encode([
            'name' => 'Toaster',
            'description' => '2-slice toaster',
            'imagePath' => 'gift-images/toaster.jpg',
            'imageBase64' => 'base64-data',
        ]));

        $fullDto = $formatter->newGiftRequest($fullPayloadRequest);
        $this->assertSame('Toaster', $fullDto->name);
        $this->assertSame('2-slice toaster', $fullDto->description);
        $this->assertSame('gift-images/toaster.jpg', $fullDto->imagePath);
        $this->assertSame('base64-data', $fullDto->imageBase64);

        $emptyPayloadRequest = new Request(content: '{}');
        $emptyDto = $formatter->newGiftRequest($emptyPayloadRequest);
        $this->assertSame('', $emptyDto->name);
        $this->assertSame('', $emptyDto->description);
        $this->assertNull($emptyDto->imagePath);
        $this->assertNull($emptyDto->imageBase64);
    }

    public function testEditDtoFromRequestMapsPayloadAndDefaults(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $formatter = new GiftRequestFormatter($urlGenerator);

        $fullPayloadRequest = new Request(content: json_encode([
            'name' => 'Mixer',
            'description' => 'Stand mixer',
            'imageBase64' => 'new-image-data',
            'removeImage' => true,
            'imagePath' => 'gift-images/mixer.jpg',
        ]));

        $fullDto = $formatter->editDtoFromRequest($fullPayloadRequest);
        $this->assertSame('Mixer', $fullDto->name);
        $this->assertSame('Stand mixer', $fullDto->description);
        $this->assertSame('new-image-data', $fullDto->imageBase64);
        $this->assertTrue($fullDto->removeImage);
        $this->assertSame('gift-images/mixer.jpg', $fullDto->imagePath);

        $emptyPayloadRequest = new Request(content: '{}');
        $emptyDto = $formatter->editDtoFromRequest($emptyPayloadRequest);
        $this->assertSame('', $emptyDto->name);
        $this->assertSame('', $emptyDto->description);
        $this->assertNull($emptyDto->imageBase64);
        $this->assertFalse($emptyDto->removeImage);
        $this->assertNull($emptyDto->imagePath);
    }
}
