<?php

namespace App\Form;

use App\Entity\Cuve;
use App\Entity\TypeCarburant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $station = $options['station'];

        $builder
            ->add('code', TextType::class, [
                'label' => 'Code de la cuve',
                'attr' => ['placeholder' => 'Ex: CUVE-01'],
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité (L)',
                'attr' => ['placeholder' => 'Ex: 5000'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description de la cuve'],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock initial (L)',
                'attr' => ['placeholder' => 'Ex: 2000'],
            ])
            ->add('typeCarburant', EntityType::class, [
                'class' => TypeCarburant::class,
                'choices' => $station->getTypeCarburants(), // récupère directement les types de carburant liés
                'choice_label' => 'name',
                'label' => 'Type de carburant',
                'placeholder' => 'Sélectionnez un type',
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cuve::class,
            'station' => null,
        ]);

        $resolver->setRequired('station'); // station obligatoire
    }
}
