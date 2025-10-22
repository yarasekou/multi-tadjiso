<?php

namespace App\Form;

use App\Entity\Structure;
use App\Entity\User;
use App\Entity\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('level', ChoiceType::class, [
            'label' => 'Niveau',
            'choices' => [
                'Super Admin (1)' => 3,
                'Admin (2)' => 4,
            ],
            'attr' => ['class' => 'form-select']
        ]);
    }
}
