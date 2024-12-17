<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\TypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_creation', null, [
                'widget' => 'single_text',
            ])
            ->add('prix')
            ->add('description')
            ->add('TypeService', EntityType::class, [
                'class' => TypeService::class, // Entité à associer
                'choice_label' => 'nom', // Doit correspondre au getter
                'placeholder' => 'Choisissez un type de service', // Optionnel
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}