<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa pokoju',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj nazwę pod jaką pokój będzie widoczny',
                    ]),
                    new Length([
                        'maxMessage' => 'Twoja nazwa nie może przekraczać {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 50
                    ]),
                ],
            ])
            ->add('logo_filename', FileType::class, [
                'label' => 'Logo pokoju',
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
                'attr' => [
                    'onchange' => 'previewFile()'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
