<?php

namespace App\Enums;

use App\Traits\Enum;

enum JobDescriptionAnalysisTypeEnum
{
    use Enum;
    case JOB_DESCRIPTION;
    case JOB_DESCRIPTION_URL;

    public function label(): string
    {
        return match ($this) {
            self::JOB_DESCRIPTION => 'Job Description',
            self::JOB_DESCRIPTION_URL => 'Job Description URL',
        };
    }
}
