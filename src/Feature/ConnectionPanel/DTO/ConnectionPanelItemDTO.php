<?php declare(strict_types=1);

namespace App\Feature\ConnectionPanel\DTO;

use App\Attributes\DTO;
use App\Feature\ConnectionPanel\Enum\ConfirmStatus;

#[DTO]
class ConnectionPanelItemDTO
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $email,
        public ConfirmStatus $status,
        public string $confirmUrl,
        public string $deleteUrl,
        public string $viewUrl,
    ) {}
}
