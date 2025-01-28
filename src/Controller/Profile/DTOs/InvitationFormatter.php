<?php declare(strict_types=1);

namespace App\Controller\Profile\DTOs;

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