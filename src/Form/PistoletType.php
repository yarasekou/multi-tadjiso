<?php

namespace App\Form;

use App\Entity\Pistolet;
use App\Entity\Pompe;
use App\Entity\TypeCarburant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PistoletType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $station = $options['station']; // récupère la station passée depuis PompeType

        $builder
            ->add('code', null, [
                'label' => 'Code du pistolet',
            ])
            ->add('indexPistolet', null, [
                'label' => 'Index du pistolet',
            ])
            ->add('typeCarburant', EntityType::class, [
                'class' => TypeCarburant::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un type de carburant',
                'query_builder' => function ($repo) use ($options) {
                    $station = $options['station'] ?? null;
                    $qb = $repo->createQueryBuilder('t');
                    if ($station) {
                        $qb->where('t.station = :station')
                            ->setParameter('station', $station);
                    }
                    return $qb;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pistolet::class,
            'station' => null, // obligatoire à passer
        ]);
    }
}
