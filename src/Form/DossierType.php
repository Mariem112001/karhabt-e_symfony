<?php

namespace App\Form;

use App\Entity\Dossier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cin', null, [
                'label' => 'Cin',
            ])
            ->add('nom', null, [
                'label' => 'Nom',
            ])
            ->add('prenom', null, [
                'label' => 'Prenom',
            ])
            ->add('region', null, [
                'label' => 'Region',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date Naissance',
                'widget' => 'single_text',
            ])
            ->add('montant', null, [
                'label' => 'Montant',
            ])
            ->add('demandeDossier', null, [
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dossier::class,
        ]);
    }
}
