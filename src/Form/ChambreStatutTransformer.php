<?php

namespace App\Form\DataTransformer;

use App\Enum\ChambreStatut;
use Symfony\Component\Form\DataTransformerInterface;

class ChambreStatutTransformer implements DataTransformerInterface
{
    // Transformer de l'énumération vers une chaîne
    public function transform($value): string
    {
        // Vérifie si la valeur est nulle et renvoie une chaîne vide
        return $value ? $value->value : '';
    }

    // Transformer de la chaîne vers l'énumération
    public function reverseTransform($value): ?ChambreStatut
    {
        return $value ? ChambreStatut::from($value) : null;
    }
}
