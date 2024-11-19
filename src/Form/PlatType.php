<?php
namespace App\Form;

use App\Entity\Plat;
use App\Entity\CategoriePlat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomPlat', TextType::class)
            ->add('descPlat', TextareaType::class, ['required' => false])
            ->add('prixPlat', NumberType::class)
            ->add('typeCuisine', TextType::class)
            ->add('dispoPlat', CheckboxType::class, ['required' => false])
            ->add('categoriePlat', EntityType::class, [
                'class' => CategoriePlat::class,
                'choice_label' => 'nomCategorie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}