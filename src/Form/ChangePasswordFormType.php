<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Podaj hasło',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Twoje hasło musi mieć co najmniej {{ limit }} znaków',
                            'maxMessage' => 'Twoje hasło nie może przekraczać {{ limit }} znaków',
                            // max length allowed by Symfony for security reasons
                            'max' => 30,
                        ]),
                    ],
                    'label' => 'Nowe hasło',
                ],
                'second_options' => [
                    'label' => 'Potwierdź hasło',
                ],
                'invalid_message' => 'Hasła muszą być identyczne',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
