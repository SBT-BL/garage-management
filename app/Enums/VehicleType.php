<?php

namespace App\Enums;

enum VehicleType: string
{
    case Car = 'Car';
    case Bike = 'Bike';
    case Scooter = 'Scooter';
    case Other = 'Other';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
