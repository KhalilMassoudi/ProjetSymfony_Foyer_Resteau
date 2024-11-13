<?php

namespace App\Enum;

enum ChambreStatut: string
{
    case DISPONIBLE = 'disponible';
    case OCCUPEE = 'occupee';
    case EN_MAINTENANCE = 'en_maintenance';
}
