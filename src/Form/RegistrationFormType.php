<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Imię i nazwisko',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj imię i nazwisko',
                    ]),
                    new Length([
                        'maxMessage' => 'Twoje imię i nazwisko nie może przekraczać {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 50
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj adres e-mail',
                    ]),
                    new Length([
                        'maxMessage' => 'Twój adres e-mail nie może przekraczać {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 180
                    ])
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Hasło',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj hasło',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Twoje hasło musi mieć conajmniej {{ limit }} znaków',
                        'maxMessage' => 'Twoje hasło nie może przekraczać {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 20
                    ])
                ]
            ])
            ->add('confirmationPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Potwierdź hasło',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Potwierdź hasło',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Twoje hasło musi mieć conajmniej {{ limit }} znaków',
                        'maxMessage' => 'Twoje hasło nie może przekraczać {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 20
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Akceptuję warunki',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Musisz zaakceptować warunki',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
