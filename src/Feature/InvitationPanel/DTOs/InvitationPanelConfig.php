<?php declare(strict_types=1);

namespace App\Feature\InvitationPanel\DTOs;

use App\Attributes\ArrayOf;
use App\Attributes\DTO;
use App\DTO\Invitation\InvitationListItemDTO;

#[DTO]
class InvitationPanelConfig
{
    /**
     * @param InvitationListItemDTO[] $invitationList
     */
    public function __construct(
        public string $createInvitationUrl,
        #[ArrayOf(InvitationListItemDTO::class)]
        public array $invitationList,
    ) {}
}