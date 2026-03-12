<?php declare(strict_types=1);

namespace App\Tests\Controller\Rest\v1\Registration;

use App\Controller\Rest\v1\Registration\RegistrationController;
use App\DTO\Registration\RegistrationRequestDTO;
use App\Entity\Invitation;
use App\Entity\User;
use App\Formatter\Registration\RegistrationFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends TestCase
{
    public function testHandleRegistrationReturnsBadRequestWhenDataIsIncomplete(): void
    {
        $request = new Request();
        $controller = new RegistrationController();

        $registrationFormatter = $this->createMock(RegistrationFormatter::class);
        $registrationFormatter->method('fromRequest')->with($request)->willReturn(
            new RegistrationRequestDTO('', 'password', 'invitation-code')
        );

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $response = $controller->handleRegistration($request, $passwordHasher, $entityManager, $registrationFormatter);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Not enough information', $response->getContent());
    }

    public function testHandleRegistrationReturnsBadRequestForInvalidInvitation(): void
    {
        $request = new Request();
        $controller = new RegistrationController();

        $registrationFormatter = $this->createMock(RegistrationFormatter::class);
        $registrationFormatter->method('fromRequest')->with($request)->willReturn(
            new RegistrationRequestDTO('user@example.com', 'password', 'invitation-code')
        );

        $invitationRepository = $this->createMock(EntityRepository::class);
        $invitationRepository->method('findOneBy')->with([
            'invitationCode' => 'invitation-code',
            'used' => false,
        ])->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(Invitation::class)->willReturn($invitationRepository);

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $response = $controller->handleRegistration($request, $passwordHasher, $entityManager, $registrationFormatter);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Invalid or used invitation', $response->getContent());
    }

    public function testHandleRegistrationCreatesUserAndConnectionForValidInvitation(): void
    {
        $request = new Request();
        $controller = new RegistrationController();

        $registrationFormatter = $this->createMock(RegistrationFormatter::class);
        $registrationFormatter->method('fromRequest')->with($request)->willReturn(
            new RegistrationRequestDTO('user@example.com', 'password', 'valid-code')
        );

        $inviter = new User();
        $inviter->setEmail('inviter@example.com');

        $invitation = new Invitation();
        $invitation->setEmail('user@example.com');
        $invitation->setInvitationCode('valid-code');
        $invitation->setInviter($inviter);
        $invitation->setUsed(false);

        $invitationRepository = $this->createMock(EntityRepository::class);
        $invitationRepository->method('findOneBy')->with([
            'invitationCode' => 'valid-code',
            'used' => false,
        ])->willReturn($invitation);

        $persisted = [];

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->with(Invitation::class)->willReturn($invitationRepository);
        $entityManager->expects($this->exactly(3))
            ->method('persist')
            ->with($this->callback(function (object $entity) use (&$persisted): bool {
                $persisted[] = $entity;
                return true;
            }));
        $entityManager->expects($this->exactly(3))->method('flush');

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'password')
            ->willReturn('hashed-password');

        $response = $controller->handleRegistration($request, $passwordHasher, $entityManager, $registrationFormatter);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('User registered successfully', $response->getContent());
        $this->assertCount(3, $persisted);
        $this->assertInstanceOf(User::class, $persisted[0]);
        $this->assertInstanceOf(Invitation::class, $persisted[1]);
        $this->assertTrue($invitation->isUsed());
        $this->assertSame('hashed-password', $persisted[0]->getPassword());
        $this->assertInstanceOf(\App\Entity\Connection::class, $persisted[2]);
    }
}
