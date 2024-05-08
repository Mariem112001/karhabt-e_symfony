<?php

namespace App\Form;

use App\Entity\Arrivage;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class ArrivageType extends AbstractType
{
     public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite')
            ->add('dateentree')
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => 'Modele', // Utilisation de la méthode getMarqueModele() de l'entité Voiture
                'placeholder' => 'Choisir une voiture',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Arrivage::class,
        ]);
    }
}
