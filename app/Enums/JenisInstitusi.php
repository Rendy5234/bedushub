<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisInstitusi: string implements HasLabel
{
    case Smk = 'SMK';
    case Sma = 'SMA';
    case Pt = 'PT';

    public function getLabel(): string
    {
        return match ($this) {
            self::Smk => 'SMK',
            self::Sma => 'SMA',
            self::Pt => 'PT',
        };
    }
}
