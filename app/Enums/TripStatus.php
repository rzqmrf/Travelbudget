<?php

namespace App\Enums;

enum TripStatus: string
{
    case Planning = 'planning';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Planning => 'Perencanaan',
            self::Active => 'Aktif',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Planning => 'gray',
            self::Active => 'blue',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }
}
