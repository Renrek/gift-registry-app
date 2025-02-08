<?php declare(strict_types=1);

namespace App\Controller\Invitation;

use App\Controller\Invitation\DTOs\InvitationListItemDTO;

class InvitationFormatter
{

    /**
     * @param array<int, \App\Entity\Invitation> $invitations
     * @return array<int, InvitationListItemDTO>
     */
    public function fromModels(array $invitations): array
    {
        $requests = [];
        foreach ($invitations as $invitation) {

            if (!$invitation->getId() || !$invitation->getEmail() || !$invitation->getInvitationCode()) {
                throw new \DomainException('Invitation must have an ID, email and code.');
            }

            $requests[] = new InvitationListItemDTO(
                id: $invitation->getId(),
                email: $invitation->getEmail(),
                isUsed: $invitation->isUsed(),
                code: $invitation->getInvitationCode(),
            );
        }

        return $requests;
    }
}