<?php declare(strict_types=1);

namespace App\Feature\ConnectionPanel\DTOs;

use App\Attributes\ArrayOf;
use App\Attributes\DTO;

#[DTO]
class ConnectionPanelConfig
{
    public function __construct(
        public string $searchUrl,
        public string $addUrl,
        #[ArrayOf(ConnectionPanelItemDTO::class)]
        public array $connectedUsers,
    ) {}
}