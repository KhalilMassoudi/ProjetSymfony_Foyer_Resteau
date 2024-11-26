<?php

namespace App\Form;

use App\Entity\Plat;
use App\Entity\CategoriePlat; // Import de l'entité CategoriePlat
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPlat', TextType::class, [
                'label' => 'Nom du plat',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le nom du plat',
                ],
            ])
            ->add('descPlat', TextType::class, [
                'label' => 'Description du plat',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez la description du plat',
                ],
                'required' => false,
            ])
            ->add('prixPlat', MoneyType::class, [
                'label' => 'Prix du plat',
                'currency' => 'TND',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('typeCuisine', TextType::class, [
                'label' => 'Type de cuisine',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le type de cuisine',
                ],
            ])
            ->add('dispoPlat', CheckboxType::class, [
                'label' => 'Disponible',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            ->add('categoriePlat', EntityType::class, [
                'class' => CategoriePlat::class, // L'entité liée
                'choice_label' => 'nomCategorie', // Affiche le champ "nomCategorie" pour chaque catégorie
                'placeholder' => 'Sélectionnez une catégorie', // Option pour afficher une valeur vide
                'label' => 'Catégorie associée', // L'étiquette pour le champ
                'attr' => [
                    'class' => 'form-control',
                    'data-live-search' => 'true', // Fonction de recherche dans le dropdown (facultatif)
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
