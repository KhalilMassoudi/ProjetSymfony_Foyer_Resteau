<?php

namespace App\Form;

use App\Entity\Chambre; // Import pour l'association avec Chambre
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheEquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEquipementB', TextType::class, [
                'label' => 'Nom de l\'équipement',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par nom',
                ],
            ])
            ->add('etatEquipementB', TextType::class, [
                'label' => 'État de l\'équipement',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher par état',
                ],
            ])
            ->add('chambre', EntityType::class, [
                'class' => Chambre::class,
                'choice_label' => 'numeroChB',
                'label' => 'Chambre associée',
                'required' => false,
                'placeholder' => 'Toutes les chambres',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }
}
