<?php

namespace App\Tests\Service;

use App\Entity\Invitation;
use App\Entity\User;
use App\Service\InvitationService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class InvitationServiceTest extends TestCase
{
    public function testCreateInvitation(): void
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $security = $this->createMock(Security::class);
        $uuidService = $this->createMock(UuidService::class);

        $user = $this->createMock(User::class);
        $security->method('getUser')->willReturn($user);
        $uuidService->method('generateV1UUID')->willReturn('uuid-1234');

        
        $invitationService = new InvitationService($entityManager, $security, $uuidService);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Invitation::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $invitation = $invitationService->createInvitation('test@example.com');

        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->assertEquals('test@example.com', $invitation->getEmail());
        $this->assertEquals($user, $invitation->getInviter());
        $this->assertEquals('uuid-1234', $invitation->getInvitationCode());
    }

    public function testCreateInvitationWithoutUser(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $uuidService = $this->createMock(UuidService::class);

        $security->method('getUser')->willReturn(null);
        $uuidService->method('generateV1UUID')->willReturn('uuid-1234');

        $invitationService = new InvitationService($entityManager, $security, $uuidService);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Invitation::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $invitation = $invitationService->createInvitation('test@example.com');

        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->assertEquals('test@example.com', $invitation->getEmail());
        $this->assertNull($invitation->getInviter());
        $this->assertEquals('uuid-1234', $invitation->getInvitationCode());
    }
}