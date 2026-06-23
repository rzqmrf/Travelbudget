<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Bbm = 'bbm';
    case Makan = 'makan';
    case Parkir = 'parkir';
    case Tol = 'tol';
    case Penginapan = 'penginapan';
    case OlehOleh = 'oleh_oleh';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match($this) {
            self::Bbm => 'BBM',
            self::Makan => 'Makan',
            self::Parkir => 'Parkir',
            self::Tol => 'Tol',
            self::Penginapan => 'Penginapan',
            self::OlehOleh => 'Oleh-oleh',
            self::Lainnya => 'Lainnya',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Bbm => '⛽',
            self::Makan => '🍔',
            self::Parkir => '🅿️',
            self::Tol => '🛣️',
            self::Penginapan => '🏨',
            self::OlehOleh => '🎁',
            self::Lainnya => '📦',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Bbm => '#F59E0B',
            self::Makan => '#22C55E',
            self::Parkir => '#3B82F6',
            self::Tol => '#8B5CF6',
            self::Penginapan => '#EC4899',
            self::OlehOleh => '#F97316',
            self::Lainnya => '#6B7280',
        };
    }
}
