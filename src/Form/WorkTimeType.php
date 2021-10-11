<?php

namespace App\Form;

use App\Entity\WorkTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('horaire')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('heurDebutTravaille')
            ->add('heurFinTravaille')/* 
            ->add('debutPauseMatinal')
            ->add('finPauseMatinal')
            ->add('debutPauseDejeuner')
            ->add('finPauseDejeuner')
            ->add('debutPauseMidi')
            ->add('finPauseMidi') */;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
        ]);
    }
}
