<?php

namespace App\Traits;

trait Enum
{
    public static function from($value): self
    {
        return self::{$value};
    }
}
