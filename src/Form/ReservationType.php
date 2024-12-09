<?php
// src/Form/ReservationType.php
namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateArrivee', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text'
            ])
            ->add('dateDepart', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text'
            ])
            ->add('nomEtudiant', TextType::class, [
                'label' => 'Nom du Etudiant'
            ])
            ->add('emailEtudiant', TextType::class, [
                'label' => 'Email du Etudiant'
            ])
            ->add('telephoneEtudiant', TextType::class, [
                'label' => 'Téléphone du Etudiant',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
