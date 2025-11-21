<?php

namespace App\Form;

use App\Entity\Stockage;
use App\Entity\Cuve;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['placeholder' => 'Quantité en L'],
            ])
            ->add('purchasePrice', MoneyType::class, [
                'label' => 'Prix d\'achat',
                'currency' => 'XOF',
                'attr' => ['placeholder' => 'Prix par L'],
            ])
            ->add('missingQuantity', IntegerType::class, [
                'label' => 'Quantité manquante',
                'required' => false,
                'attr' => ['placeholder' => 'Si applicable'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stockage::class,
            'typeCarburant' => null, // obligatoire pour filtrer les cuves
        ]);
        $resolver->setRequired('typeCarburant');
    }
}
