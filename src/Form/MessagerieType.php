<?php

namespace App\Form;

use App\Entity\Messagerie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('dateenvoie')
            ->add('vu')
            ->add('deleted')
            ->add('sender')
            ->add('receiver')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messagerie::class,
        ]);
    }
}
