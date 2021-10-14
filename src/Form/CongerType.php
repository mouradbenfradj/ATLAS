<?php

namespace App\Form;

use App\Entity\Conger;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CongerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('debut', DateType::class , [
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('fin',DateType::class, [
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('demiJourner', CheckboxType::class,[
                'required'=>false
            ])
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'choices' => [
                    "Conger payer" => "CP",
                    "Conger maladie" => "CM",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conger::class,
        ]);
    }
}
