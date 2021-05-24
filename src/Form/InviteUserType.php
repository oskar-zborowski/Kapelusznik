<?php

namespace App\Form;

use App\Entity\RoomConnection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class InviteUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Kod użytkownika',
                'data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj kod użytkownika, którego chcesz dodać',
                    ]),
                    new Length([
                        'maxMessage' => 'Kod musi składać się z {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 6,
                        'minMessage' => 'Kod musi składać się z {{ limit }} znaków',
                        // min length allowed by database field
                        'min' => 6
                    ]),
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoomConnection::class,
        ]);
    }
}
