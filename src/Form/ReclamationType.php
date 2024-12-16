<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\TypeReclamation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;


class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ titre
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
                ]])
            // Champ TypeReclamation (dropdown)
            ->add('TypeReclamations', EntityType::class, [
                'class' => TypeReclamation::class,
                'choice_label' => 'NomTypeReclamation',  // Adjust as needed based on your TypeReclamation entity
                'label' => 'Type de réclamation',
                'attr' => [
                    'class' => 'form-control form-control-lg',
                ],
                'required' => true,
            ])
            // Champ Image (avec validation correcte)
            ->add('image', FileType::class, [
                'label' => 'Image de réclamation',
                'required' => false, // Champ facultatif
                'mapped' => false, // Non mappé directement à l'entité
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            // Bouton de soumission
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter la réclamation',
            ]);
            // Bouton de soumission

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
