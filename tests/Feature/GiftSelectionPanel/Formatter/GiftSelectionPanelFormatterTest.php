<?php declare(strict_types=1);

namespace App\Tests\Feature\GiftSelectionPanel\Formatter;

use App\Entity\GiftRequest;
use App\Feature\GiftSelectionPanel\Formatter\GiftSelectionPanelFormatter;
use PHPUnit\Framework\TestCase;

class GiftSelectionPanelFormatterTest extends TestCase
{
    public function testFromEntityThrowsWhenGiftIdMissing(): void
    {
        $formatter = new GiftSelectionPanelFormatter();
        $gift = $this->createMock(GiftRequest::class);
        $gift->method('getId')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No Gift ID provided');

        $formatter->fromEntity($gift);
    }

    public function testFromEntityMapsGiftToDto(): void
    {
        $formatter = new GiftSelectionPanelFormatter();
        $gift = $this->createMock(GiftRequest::class);
        $gift->method('getId')->willReturn(42);
        $gift->method('getName')->willReturn('Coffee Grinder');
        $gift->method('getDescription')->willReturn('Burr grinder');
        $gift->method('getImagePath')->willReturn('gift-images/grinder.jpg');

        $dto = $formatter->fromEntity($gift);

        $this->assertSame(42, $dto->giftId);
        $this->assertSame('Coffee Grinder', $dto->name);
        $this->assertSame('Burr grinder', $dto->description);
        $this->assertSame('', $dto->claimUrl);
        $this->assertSame('gift-images/grinder.jpg', $dto->imagePath);
    }

    public function testFromEntityListMapsAllGifts(): void
    {
        $formatter = new GiftSelectionPanelFormatter();

        $firstGift = $this->createMock(GiftRequest::class);
        $firstGift->method('getId')->willReturn(1);
        $firstGift->method('getName')->willReturn('First');
        $firstGift->method('getDescription')->willReturn('First gift');
        $firstGift->method('getImagePath')->willReturn('gift-images/first.jpg');

        $secondGift = $this->createMock(GiftRequest::class);
        $secondGift->method('getId')->willReturn(2);
        $secondGift->method('getName')->willReturn('Second');
        $secondGift->method('getDescription')->willReturn('Second gift');
        $secondGift->method('getImagePath')->willReturn('gift-images/second.jpg');

        $result = $formatter->fromEntityList([$firstGift, $secondGift]);

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]->giftId);
        $this->assertSame(2, $result[1]->giftId);
    }
}
