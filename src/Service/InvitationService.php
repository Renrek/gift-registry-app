<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class InvitationService
{
    private EntityManagerInterface $entityManager;
    private Security $security;
    private UuidService $uuidService;

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

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User must be logged in to create an invitation');
        }
        
        $invitation->setInviter($user);
        $invitation->setInvitationCode($this->uuidService->generateV1UUID());

        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $invitation;
    }
}