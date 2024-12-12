<?php

namespace App\Form\DataTransformer;

use App\Enum\ChambreStatut;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EnumToStringTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        // Convertit l'énumération en chaîne de caractères pour affichage dans le formulaire
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ChambreStatut) {
            throw new TransformationFailedException('Expected a ChambreStatut instance.');
        }

        return $value->value;
    }

    public function reverseTransform($value): ?ChambreStatut
    {
        // Convertit la chaîne de caractères envoyée dans le formulaire en énumération
        if (null === $value || '' === $value) {
            return null;
        }

        try {
            return ChambreStatut::from($value);
        } catch (\ValueError $e) {
            throw new TransformationFailedException(sprintf('Invalid value "%s" for ChambreStatut.', $value));
        }
    }
}