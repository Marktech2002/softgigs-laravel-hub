<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRoles: string
{
    use \App\Traits\EnumHelpers;

    case USER = 'user';
    case ADMIN = 'admin';
}
