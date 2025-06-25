<?php

namespace App\Enums;

use App\Traits\Enum;

enum JobDescriptionAnalysisStatusEnum
{
    use Enum;
    case PENDING;
    case IN_PROGRESS;
    case COMPLETED;
    case FAILED;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'The process is waiting to start.',
            self::IN_PROGRESS => 'The process is in progress.',
            self::COMPLETED => 'The process is completed.',
            self::FAILED => 'The process failed.',
        };
    }
}
