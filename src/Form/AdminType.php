<?php

namespace App\Form;

use App\Entity\Structure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminType extends BaseUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('structure', EntityType::class, [
            'class' => Structure::class,
            'choice_label' => 'name',
            'label' => 'Structure',
            'required' => true,
            'attr' => ['class' => 'form-select']
        ]);
    }
}
