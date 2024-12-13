<?php

namespace App\Enum;

enum ChambreStatut: string
{
    case DISPONIBLE = 'Disponible';
    case OCCUPEE = 'Occupée';
    case EN_MAINTENANCE = 'En maintenance';

    public function getValue(): string
    {
        return $this->value;
    }
    public static function disponible(): string
    {
        return ChambreStatut::DISPONIBLE->getValue();
    }

    public static function occupee(): string
    {
        return ChambreStatut::OCCUPEE->value;
    }

    public static function enMaintenance(): string
    {
        return ChambreStatut::EN_MAINTENANCE->value;
    }
}
