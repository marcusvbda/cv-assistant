<?php

namespace App\Traits;

trait Enum
{
    public static function from($value): self
    {
        return self::{$value};
    }

    public function label(): string
    {
        return $this->name;
    }

    public static function values(): array
    {
        $items = self::cases();
        $values = [];

        foreach ($items as $item) {
            $values[$item->name] = static::from($item->name)->label();
        }

        return $values;
    }
}
