<?php 
// src/Form/CommentaireFormType.php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;


class CommentaireFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenue', TextareaType::class, [
                'label' => 'Commentaire:',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/\belephant\b/i', // Match the word "elephant" (case insensitive)
                        'match' => false,
                        'message' => 'Please refrain from using inappropriate language.',
                    ]),
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => '',
                'attr' => ['style' => 'display: none;'], // Hide the input field
                'data' => new \DateTime(), // Set default value as a DateTime object
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
            // Add an option to receive the actualite object
            'actualite' => null,
        ]);
    }
}
