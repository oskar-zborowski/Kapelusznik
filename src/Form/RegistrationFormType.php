<?php

namespace App\Form;

use App\Entity\User;
use App\Service\AgreementService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    private $agreementService;

    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

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
                ],
            ])
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
                    'label' => 'Hasło',
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

        $agreement = $this->agreementService->findCurrentAgreements();

        foreach ($agreement as $a) {
            if ($a->getIsRequired()) {
                $builder
                    ->add('agr' . $a->getId(), CheckboxType::class, [
                        'label' => false,
                        'mapped' => false,
                        'constraints' => [
                            new IsTrue([
                                'message' => 'Musisz zaakceptować to pole',
                            ]),
                        ],
                    ])
                ;
            } else {
                $builder
                    ->add('agr' . $a->getId(), CheckboxType::class, [
                        'label' => false,
                        'mapped' => false,
                        'required' => false,
                    ])
                ;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
