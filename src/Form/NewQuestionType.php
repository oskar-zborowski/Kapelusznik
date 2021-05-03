<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, [
                'label' => 'Treść',
                'data' => '',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj treść pytania',
                    ]),
                    new Length([
                        'maxMessage' => 'Twoje pytanie nie może przekraczać {{ limit }} znaków',
                        // max length allowed by database field
                        'max' => 255
                    ]),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Kategoria',
                'data' => 'OT',
                'choices' => [
                    'Życiowe tematy - pytania ogólne' => 'OT',
                    'Jazda bez trzymanki - najbardziej niezręczne pytania' => 'JBT',
                    'Mów mi więcej - słodzenie sobie' => 'MMW',
                    '18+ - kontekst erotyczny' => '18',
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Płeć',
                'data' => 'a',
                'choices' => [
                    'Wszyscy' => 'a',
                    'Mężczyzna' => 'm',
                    'Kobieta' => 'f',
                ],
            ])
            ->add('is_public', ChoiceType::class, [
                'label' => 'Czy pytanie ma zostać upublicznione?',
                'data' => 1,
                'choices' => [
                    'Tak, niech inni też korzystają' => 1,
                    'Nie, tylko ja mam je widzieć' => 0,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
