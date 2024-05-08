<?php

namespace App\Form;

use App\Entity\DemandeDossier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeDossierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('urlcin', FileType::class, [
                'label' => 'CIN',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('urlcerretenu', FileType::class, [
                'label' => 'Cerificat de résidence',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('urlatttravail', FileType::class, [
                'label' => 'Attestation de travail',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('urldecrevenu', FileType::class, [
                'label' => 'Déclaration de revenu',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
            ->add('urlextnaissance', FileType::class, [
                'label' => 'Extrait de naissance',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeDossier::class,
        ]);
    }
}
