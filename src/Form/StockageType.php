<?php

namespace App\Form;

use App\Entity\Cuve;
use App\Entity\GlobalStockage;
use App\Entity\Stockage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la quantité disponible'
                ]
            ])
            ->add('purchasePrice', MoneyType::class, [
                'label' => 'Prix d\'achat',
                'currency' => 'EUR',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix d\'achat par unité'
                ]
            ])
            ->add('missingQuantity', NumberType::class, [
                'label' => 'Quantité manquante',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Si applicable'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stockage::class,
        ]);
    }
}
