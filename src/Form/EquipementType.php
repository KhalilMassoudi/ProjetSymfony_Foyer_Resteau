<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEquipementB', TextType::class, [
                'label' => 'Nom de l\'équipement',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le nom de l\'équipement',
                ],
            ])
            ->add('etatEquipementB', TextType::class, [
                'label' => 'État de l\'équipement',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez l\'état de l\'équipement',
                ],
            ])
            ->add('dateDernierEntretienEquipementB', DateType::class, [
                'label' => 'Date du dernier entretien',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}
