<?php

namespace App\Form;

use App\Entity\Agreement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
                        // max length allowed by database field
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
                        // max length allowed by database field
                        'max' => 180
                    ])
                ]
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
                            'minMessage' => 'Twoje hasło musi mieć conajmniej {{ limit }} znaków',
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

        $agreement = $this->entityManager->getRepository(Agreement::class)->findBy(['in_registration_form' => 1], ['id' => 'DESC']);
        $checked = array();

        foreach ($agreement as $a) {
            $flag = false;

            for ($i=0; $i<count($checked); $i++) {
                if ($a->getSignature() == $checked[$i]->getSignature()) {
                    $flag = true;
                    break;
                }
            }

            if (!$flag && $a->getDateOfEntry() <= new \DateTime()) {
                $checked[] = $a;

                if ($a->getIsRequired()) {
                    $builder
                        ->add($a->getId(), CheckboxType::class, [
                            'label' => $a->getName(),
                            'mapped' => false,
                            'constraints' => [
                                new IsTrue([
                                    'message' => 'Musisz zaakceptować to pole',
                                ])
                            ]
                        ])
                    ;
                } else {
                    $builder
                        ->add($a->getId(), CheckboxType::class, [
                            'label' => $a->getName(),
                            'mapped' => false,
                            'required' => false,
                        ])
                    ;
                }
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
