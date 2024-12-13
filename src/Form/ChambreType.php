<?php

namespace App\Form;

use App\Entity\Chambre;
use App\Enum\ChambreStatut;
use App\Form\DataTransformer\EnumToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChambreType extends AbstractType
{
    private EnumToStringTransformer $enumToStringTransformer;

    public function __construct(EnumToStringTransformer $enumToStringTransformer)
    {
        $this->enumToStringTransformer = $enumToStringTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroChB', TextType::class, [
                'label' => 'Numéro de la chambre',
            ])
            ->add('etageChB', IntegerType::class, [
                'label' => 'Étage',
            ])
            ->add('capaciteChB', IntegerType::class, [
                'label' => 'Capacité',
            ])
            ->add('statutChB', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Disponible' => ChambreStatut::DISPONIBLE->value,
                    'Occupée' => ChambreStatut::OCCUPEE->value,
                    'En maintenance' => ChambreStatut::EN_MAINTENANCE->value,
                ],
                'required' => true,
            ])
            ->add('prixChB', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'mapped' => true, // Lie ce champ à l'entité
                'data_class' => null,
            ]);

        $builder->get('statutChB')->addModelTransformer($this->enumToStringTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}