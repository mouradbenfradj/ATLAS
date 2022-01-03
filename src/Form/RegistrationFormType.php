<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Email']
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'attr' => ['id' => 'agreeTerms', 'value' => 'agree'],
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => ['class' => 'form-control', 'placeholder' => 'new password', 'autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('id', NumberType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'User Id']
        ])
        ->add('badgenumbe', NumberType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Badge Number']
        ])
        ->add('firstName', null, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'First name']
        ])
        ->add('lastName', null, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Last name']
        ])
        ->add('debutTravaille', null, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Premier Jour de travaille']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}