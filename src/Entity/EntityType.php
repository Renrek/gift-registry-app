<?php declare(strict_types=1);

namespace App\Entity;

enum EntityType: string
{
    case CONNECTION = 'connection';
    case GIFT_REQUEST = 'gift_request';
    case USER = 'user';
    case GIFT_CLAIM = 'gift_claim';
    case INVITATION = 'invitation';
}