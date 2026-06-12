<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;

enum JobTags: string
{
    use EnumHelpers;

    case FULL_TIME = 'Full Time';
    case PART_TIME = 'Part Time';
    case CONTRACT = 'Contract';
    case FREELANCE = 'Freelance';
    case REMOTE = 'Remote';
    case INTERNSHIP = 'Internship';
}
