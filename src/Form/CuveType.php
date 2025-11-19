<?php

namespace App\Form;

use App\Entity\Cuve;
use App\Entity\Station;
use App\Entity\TypeCarburant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $station = $options['station']; // Récupération de la station passée depuis le contrôleur

        $builder
            ->add('code')
            ->add('capacity', null, ['label' => 'Capacité (L)'])
            ->add('description')
            ->add('stock')
            ->add('typeCarburant', EntityType::class, [
                'class' => TypeCarburant::class,
                'choice_label' => 'name',
                'label' => 'Type de carburant',
                'query_builder' => function (EntityRepository $er) use ($station) {
                    return $er->createQueryBuilder('t')
                        ->where('t.station = :station')
                        ->setParameter('station', $station);
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cuve::class,
            'station' => null, // option obligatoire
        ]);
    }
}
