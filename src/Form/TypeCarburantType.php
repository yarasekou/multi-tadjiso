<?php

namespace App\Form;

use App\Entity\Station;
use App\Entity\TypeCarburant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCarburantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du carburant',
                'attr' => [
                    'placeholder' => 'Ex: Essence, Diesel, Super...',
                    'class' => 'form-control'
                ]
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'Prix unitaire (FCFA)',
                'attr' => [
                    'placeholder' => 'Prix par litre',
                    'class' => 'form-control',
                    'min' => 0
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description de la stations...',
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeCarburant::class,
        ]);
    }
}
