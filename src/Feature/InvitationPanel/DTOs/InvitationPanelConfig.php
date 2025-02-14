<?php declare(strict_types=1);

namespace App\Feature\InvitationPanel\DTOs;

use App\Attributes\ArrayOf;
use App\Attributes\DTO;
use App\Controller\Invitation\DTOs\InvitationListItemDTO;

#[DTO]
class InvitationPanelConfig
{
    public function __construct(
        public string $createInvitationUrl,
        #[ArrayOf(InvitationListItemDTO::class)]
        public array $invitationList,
    ) {}
}