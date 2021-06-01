<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\RoomQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AddQuestionType extends AbstractType
{
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $questions = $this->entityManager->getRepository(Question::class)->findBy(['creator' => $this->security->getUser()]);
        $questions2 = $this->entityManager->getRepository(Question::class)->findBy(['is_public' => 1, 'is_verified' => 1]);

        $all = null;

        foreach ($questions as $q) {
            $all['Moje pytania'][$q->getContent()] = $q->getId();
        }

        foreach ($questions2 as $q) {
            if ($q->getCreator() != $this->security->getUser())
                $all['Wszystkie pytania'][$q->getContent()] = $q->getId();
        }

        $builder
            ->add('question', ChoiceType::class, [
                'label' => 'Pytanie',
                'choices' => $all,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoomQuestion::class,
        ]);
    }
}
