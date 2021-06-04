<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\RoomConnection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AnswerType extends AbstractType
{
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $room = $this->entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->security->getUser()]);
        $users = $this->entityManager->getRepository(RoomConnection::class)->findBy(['room' => $room->getRoom()]);

        $all = null;

        foreach ($users as $u) {
            if ($u->getUser() != $this->security->getUser())
                $all[$u->getUser()->getName()] = $u->getUser()->getId();
        }

        $builder
            ->add('answer', ChoiceType::class, [
                'label' => 'Odpowiedź',
                'choices' => $all,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
