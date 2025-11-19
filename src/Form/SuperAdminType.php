<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SuperAdminType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('level', ChoiceType::class, [
            'label' => 'Niveau',
            'choices' => [
                'Super admin (1)' => 1,
                'admin (2)' => 2,
            ],
            'attr' => ['class' => 'form-select']
        ]);
    }
}
