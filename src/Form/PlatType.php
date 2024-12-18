<?php

namespace App\Form;

use App\Entity\Plat;
use App\Entity\CategoriePlat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


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
                    'placeholder' => 'Saisissez le prix du plat',
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
                'class' => CategoriePlat::class,
                'choice_label' => 'nomCategorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'label' => 'Catégorie associée',
                'attr' => [
                    'class' => 'form-control',
                    'data-live-search' => 'true',
                ],
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez la quantité',
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                    new Assert\PositiveOrZero(['message' => 'La quantité doit être un nombre positif ou zéro.']),
                ],
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',  // This is to use a single input field for DateTime
                'required' => false,        // Make it optional if you want, or set it to true if mandatory
                'input' => 'datetime',      // Store as DateTime in database
                'attr' => ['class' => 'datetime-picker'], // You can add your custom CSS class for styling
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du plat',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF).',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                    ]),
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
