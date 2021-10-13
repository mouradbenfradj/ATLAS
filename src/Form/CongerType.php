<?php

namespace App\Form;

use App\Entity\Conger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CongerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debut',null, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fin',null, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('demiJourner')
            ->add('type', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    "CP" => "CP",
                    "CM" => "CM",
                ],
            ])
            //->add('employer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conger::class,
        ]);
    }
}
