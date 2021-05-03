<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj nazwę pod jaką będziesz widoczny',
                    ]),
                    new Length([
                        'maxMessage' => 'Twoja nazwa nie może przekraczać {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 50
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj adres e-mail',
                    ]),
                    new Length([
                        'maxMessage' => 'Twój adres e-mail nie może przekraczać {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 180,
                    ])
                ]
            ])
            ->add('profile_picture', FileType::class, [
                'label' => 'Zdjęcie profilowe',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*'
                        ],
                        'maxSizeMessage' => 'Rozmiar zdjęcia nie może przekraczać 2MB!',
                        'mimeTypesMessage' => 'Obsługiwany format pliku musi być obrazem'
                    ])
                ],
                'required' => false
            ])
            ->add('date_of_birth', DateType::class, [
                'label' => 'Data urodzenia',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Płeć',
                'placeholder' => null,
                'required' => false,
                'choices' => [
                    '--wybierz--' => null,
                    'Kobieta' => 'f',
                    'Mężczyzna' => 'm'
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
