<?php

namespace App\Form;

use App\Entity\DemandePlat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandePlatFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')   // Description de la demande
            ->add('nom')           // Nom de la personne qui fait la demande
            ->add('prenom')        // Prénom de la personne qui fait la demande
            ->add('telephone')     // Téléphone de la personne
            ->add('email')         // Email de la personne
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandePlat::class, // L'entité à associer
        ]);
    }
}
