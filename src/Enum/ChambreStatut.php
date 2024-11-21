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
}

// Utilisation
try {
    $statutString = 'Disponible';
    $statut = ChambreStatut::from($statutString);
    echo $statut->value; // Affiche 'Disponible'
} catch (\ValueError $e) {
    echo $e->getMessage();
}