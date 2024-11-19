<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ titre
            ->add('titre', TextType::class, [
                'label' => 'Titre de la réclamation',
                'required' => true,  // Champ obligatoire
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'placeholder' => 'Entrez le titre de la réclamation', // Optionnel, texte d'exemple
                ]
            ])
            // Champ description
            ->add('description', TextareaType::class, [
                'label' => 'Description de la réclamation',
                'required' => true, // Champ obligatoire
                'attr' => [
                    'class' => 'form-control form-control-lg',
                    'style' => 'height: 150px; white-space: pre-wrap; overflow-wrap: break-word;',
                ]
            ])
            // Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter la réclamation',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
