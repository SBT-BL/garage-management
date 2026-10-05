<?php

namespace App\Enums;

enum JobCardStatus: string
{
    case Pending = 'Pending';
    case InProgress = 'In Progress';
    case Complete = 'Complete';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Bootstrap badge class for this status.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'text-bg-warning',
            self::InProgress => 'text-bg-info',
            self::Complete => 'text-bg-success',
        };
    }
}
