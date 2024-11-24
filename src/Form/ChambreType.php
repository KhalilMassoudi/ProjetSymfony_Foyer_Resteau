<?php

namespace App\Form;

use App\Entity\Chambre;
use App\Enum\ChambreStatut;
use App\Form\DataTransformer\ChambreStatutTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChambreType extends AbstractType
{
    private $chambreStatutTransformer;

    public function __construct(ChambreStatutTransformer $chambreStatutTransformer)
    {
        $this->chambreStatutTransformer = $chambreStatutTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroChB', TextType::class, [
                'label' => 'Numéro de la chambre',
                'required' => true,
            ])
            ->add('etageChB', IntegerType::class, [
                'label' => 'Étage',
                'required' => true,
            ])
            ->add('capaciteChB', IntegerType::class, [
                'label' => 'Capacité',
                'required' => true,
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
            ->add('prixChB', NumberType::class, [
                'label' => 'Prix de la chambre',
                'required' => true,
                'scale' => 2,  // Définir la précision pour les décimales
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de la chambre',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);

        // Appliquer le transformer pour le champ statut
        $builder->get('statutChB')
            ->addModelTransformer($this->chambreStatutTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
