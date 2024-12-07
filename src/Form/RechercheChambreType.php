<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroChB', TextType::class, [
                'label' => 'Numéro de la chambre',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par numéro',
                ],
            ])
            ->add('etageChB', IntegerType::class, [
                'label' => 'Étage',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par étage',
                ],
            ])
            ->add('statutChB', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'Occupée' => 'Occupée',
                    'En maintenance' => 'En maintenance',
                ],
                'required' => false,
                'placeholder' => 'Tous les statuts',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('prixChB', IntegerType::class, [
                'label' => 'Prix',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par prix',
                ],
            ]);
    }
}
