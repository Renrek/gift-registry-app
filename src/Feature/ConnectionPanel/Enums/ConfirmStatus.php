<?php declare(strict_types=1);

namespace App\Feature\ConnectionPanel\Enums;

use App\Attributes\DTOEnum;

#[DTOEnum]
enum ConfirmStatus: string
{
    case CONFIRMED = 'confirmed';
    case PENDING = 'pending';
    case NOT_CONFIRMED = 'not_confirmed';
}