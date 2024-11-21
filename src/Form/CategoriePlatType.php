<?php
namespace App\Form;

use App\Entity\CategoriePlat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriePlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCategorie', TextType::class, [
                'label' => 'Category Name',
                'required' => true,
            ])
            ->add('descrCategorie', TextareaType::class, [
                'label' => 'Category Description',
                'required' => false,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoriePlat::class,
        ]);
    }
}