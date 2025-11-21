<?php

namespace App\Form;

use App\Entity\Pompe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PompeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $station = $options['station'];

        $builder
            ->add('code', TextType::class, [
                'label' => 'Code de la pompe',
                'attr' => ['placeholder' => 'Ex: POMPE-01'],
            ])
            ->add('pistolets', CollectionType::class, [
                'entry_type' => PistoletType::class,
                'entry_options' => ['station' => $station],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => 'Pistolets',
                'attr' => ['class' => 'pistolets-collection'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pompe::class,
            'station' => null,
        ]);

        // Rendre l'option 'station' obligatoire
        $resolver->setRequired('station');
    }
}
