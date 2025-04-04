<?php declare(strict_types=1);

namespace App\Formatter\Invitation;

use App\DTO\Invitation\InvitationListItemDTO;

class InvitationFormatter
{

    /**
     * @param array<int, \App\Entity\Invitation> $invitations
     * @return array<int, InvitationListItemDTO>
     */
    public function forInvitationPanel(array $invitations): array
    {
        return array_map(fn(\App\Entity\Invitation $invitation) => 
            $this->fromEntity($invitation), $invitations
        );
    }

    public function fromEntity(\App\Entity\Invitation $invitation): InvitationListItemDTO
    {
        if (!$invitation->getId() || !$invitation->getEmail() || !$invitation->getInvitationCode()) {
            throw new \DomainException('Invitation must have an ID, email and code.');
        }

        return new InvitationListItemDTO(
            id: $invitation->getId(),
            email: $invitation->getEmail(),
            isUsed: $invitation->isUsed(),
            code: $invitation->getInvitationCode(),
        );
    }
}