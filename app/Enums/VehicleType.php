<?php

namespace App\Enums;

enum VehicleType: string
{
    case Motor = 'motor';
    case Mobil = 'mobil';

    public function label(): string
    {
        return match($this) {
            self::Motor => 'Motor',
            self::Mobil => 'Mobil',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Motor => '🏍️',
            self::Mobil => '🚗',
        };
    }
}
