<?php

namespace App\Tests\Service;

use App\Entity\Invitation;
use App\Entity\User;
use App\Service\InvitationService;
use App\Service\UuidService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;

class InvitationServiceTest extends TestCase
{
    public function testCreateInvitation(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Security&MockObject $security */
        $security = $this->createMock(Security::class);
        /** @var UuidService&MockObject $uuidService */
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

    public function testCreateInvitationWithoutUserThrowsException(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        /** @var Security&MockObject $security */
        $security = $this->createMock(Security::class);
        /** @var UuidService&MockObject $uuidService */
        $uuidService = $this->createMock(UuidService::class);

        $security->method('getUser')->willReturn(null);
        $uuidService->method('generateV1UUID')->willReturn('uuid-1234');

        $invitationService = new InvitationService($entityManager, $security, $uuidService);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User must be logged in to create an invitation');

        $invitationService->createInvitation('test@example.com');
    }

    public function testCreateInvitationWithDifferentEmail(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Security&MockObject $security */
        $security = $this->createMock(Security::class);
        /** @var UuidService&MockObject $uuidService */
        $uuidService = $this->createMock(UuidService::class);

        $user = $this->createMock(User::class);
        $security->method('getUser')->willReturn($user);
        $uuidService->method('generateV1UUID')->willReturn('uuid-5678');

        $invitationService = new InvitationService($entityManager, $security, $uuidService);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Invitation::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $invitation = $invitationService->createInvitation('another@example.com');

        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->assertEquals('another@example.com', $invitation->getEmail());
        $this->assertEquals($user, $invitation->getInviter());
        $this->assertEquals('uuid-5678', $invitation->getInvitationCode());
    }
}