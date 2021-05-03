<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserAgreement;
use App\Service\AgreementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BasicUserDataType extends AbstractType
{
    private $entityManager;
    private $agreementService;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, AgreementService $agreementService, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->agreementService = $agreementService;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $this->security->getUser()]);
        $userAgreement = $this->entityManager->getRepository(UserAgreement::class)->findBy(['user' => $this->security->getUser(), 'cancellation_date' => NULL]);

        if ($user->getDateOfBirth() != NULL)
            $dateOfBirth = $user->getDateOfBirth();
        else
            $dateOfBirth = NULL;

        if ($user->getGender() != NULL)
            $gender = $user->getGender();
        else
            $gender = NULL;

        if ($user->getExternalLoginForm() != NULL)
            $externalLoginForm = $user->getExternalLoginForm();
        else
            $externalLoginForm = NULL;

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'data' => $user->getName(),
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
            ->add('date_of_birth', BirthdayType::class, [
                'label' => 'Data urodzenia',
                'widget' => 'single_text',
                'data' => $dateOfBirth,
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Płeć',
                'data' => $gender,
                'choices' => [
                    '-- wybierz --' => NULL,
                    'Mężczyzna' => 'm',
                    'Kobieta' => 'f',
                ],
            ])
            ->add('profile_picture', FileType::class, [
                'label' => 'Zdjęcie profilowe',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'maxSizeMessage' => 'Rozmiar zdjęcia nie może przekraczać 2MB!',
                        'mimeTypesMessage' => 'Obsługiwany format pliku musi być obrazem',
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'data' => $user->getEmail(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj adres e-mail',
                    ]),
                    new Length([
                        'maxMessage' => 'Twój adres e-mail nie może przekraczać {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 180,
                    ]),
                ],
                'mapped' => false,
            ])
        ;

        if ($externalLoginForm == 'f') {
            $builder
                ->add('active_login_form', ChoiceType::class, [
                    'label' => 'Rodzaj logowania',
                    'data' => $user->getActiveLoginForm(),
                    'choices' => [
                        'Standardowo' => 's',
                        'Facebook' => 'f',
                    ],
                ])
            ;
        } else if ($externalLoginForm == 'g') {
            $builder
                ->add('active_login_form', ChoiceType::class, [
                    'label' => 'Rodzaj logowania',
                    'data' => $user->getActiveLoginForm(),
                    'choices' => [
                        'Standardowo' => 's',
                        'Google' => 'g',
                    ],
                ])
            ;
        } else {
            $builder
                ->add('active_login_form', ChoiceType::class, [
                    'label' => 'Rodzaj logowania',
                    'disabled' => true,
                    'data' => $user->getActiveLoginForm(),
                    'choices' => [
                        'Standardowo' => 's',
                    ],
                ])
            ;
        }

        $agreement = $this->agreementService->findCurrentAgreements(false);

        foreach ($agreement as $a) {
            $found = false;

            foreach ($userAgreement as $ua) {
                if ($a->getSignature() == $ua->getAgreement()->getSignature()) {
                    if ($a->getIsRequired()) {
                        $builder
                            ->add('agr' . $a->getId(), CheckboxType::class, [
                                'label' => false,
                                'disabled' => true,
                                'data' => true,
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
                                'data' => true,
                                'mapped' => false,
                                'required' => false,
                            ])
                        ;
                    }

                    $found = true;
                }
            }
            
            if (!$found) {
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
