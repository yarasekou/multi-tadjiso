<?php

namespace App\Form;

use App\Entity\Pompe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class PompeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('pistolets', CollectionType::class, [
                'entry_type' => PistoletType::class,
                'entry_options' => ['station' => $options['station']], // <-- important
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pompe::class,
            'station' => null, // obligatoire à passer depuis le contrôleur
        ]);
    }
}
