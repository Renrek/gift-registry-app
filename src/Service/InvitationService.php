<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class InvitationService
{
    private $entityManager;
    private $security;
    private $uuidService;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UuidService $uuidService)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->uuidService = $uuidService;
    }

    public function createInvitation(string $email): Invitation
    {
        $invitation = new Invitation();
        $invitation->setEmail($email);
        $invitation->setInviter($this->security->getUser());
        $invitation->setInvitationCode($this->uuidService->generateV1UUID());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $invitation;
    }
}