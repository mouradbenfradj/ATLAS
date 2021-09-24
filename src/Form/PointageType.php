<?php

namespace App\Form;

use App\Entity\Pointage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('entrer')
            ->add('sortie')
            ->add('nbrHeurTravailler')
            ->add('retardEnMinute')
            ->add('departAnticiper')
            ->add('retardMidi')
            ->add('totaleRetard')
            ->add('autorisationSortie')
            ->add('congerPayer')
            ->add('abscence')
            ->add('heurNormalementTravailler')
            ->add('diff')
            ->add('employer')
            ->add('horaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pointage::class,
        ]);
    }
}
