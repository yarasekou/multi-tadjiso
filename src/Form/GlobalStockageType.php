<?php

namespace App\Form;

use App\Entity\GlobalStockage;
use App\Entity\TypeCarburant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GlobalStockageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité (L)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la quantité totale'
                ]
            ])
            ->add('missingQuantity', IntegerType::class, [
                'label' => 'Quantité manquante (L)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la quantité manquante'
                ]
            ])
            ->add('purchasePrice', IntegerType::class, [
                'label' => 'Prix d\'achat (FCFA)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le prix unitaire'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GlobalStockage::class,
        ]);
    }
}
