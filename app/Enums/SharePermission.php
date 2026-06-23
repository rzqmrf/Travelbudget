<?php

namespace App\Enums;

enum SharePermission: string
{
    case View = 'view';
    case Edit = 'edit';

    public function label(): string
    {
        return match ($this) {
            self::View => 'Hanya Lihat',
            self::Edit => 'Lihat & Edit',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::View => 'blue',
            self::Edit => 'green',
        };
    }
}
