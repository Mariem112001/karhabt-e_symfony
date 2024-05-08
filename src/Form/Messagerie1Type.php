<?php

namespace App\Form;

use App\Entity\Messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class Messagerie1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('vu')
            ->add('deleted')
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Display the username in the dropdown list
                'placeholder' => 'Choose a receiver', // Placeholder text for the dropdown list
                // You can add more options here, such as query builder to filter users if needed
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messagerie::class,
        ]);
    }
}
