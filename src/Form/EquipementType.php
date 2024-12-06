<?php
namespace App\Form;

use App\Entity\Equipement;
use App\Entity\Chambre; // Import de l'entité Chambre
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Import de FileType
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
            ])
            ->add('chambre', EntityType::class, [
                'class' => Chambre::class, // L'entité liée
                'choice_label' => 'numeroChB', // Affiche le champ "numeroChB" pour chaque chambre
                'placeholder' => 'Sélectionnez une chambre', // Option pour afficher une valeur vide
                'label' => 'Chambre associée', // L'étiquette pour le champ
                'attr' => [
                    'class' => 'form-control',
                    'data-live-search' => 'true', // Ajoute une fonctionnalité de recherche dans le dropdown (facultatif)
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'équipement', // Corrigé : Image de l'équipement
                'required' => false, // Champ facultatif
                'mapped' => false, // Non mappé directement à l'entité
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

