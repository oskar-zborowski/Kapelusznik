<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Room;
use App\Entity\RoomConnection;
use App\Entity\RoomQuestion;
use App\Entity\User;
use App\Form\AnswerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game", name="game")
     */
    public function index(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('app_login');

        $confirm = false;
        $answerNumber = 0;
        $table = null;

        $entityManager = $this->getDoctrine()->getManager();

        if ($session->get('activeRoom')) {
            $roomNumber = $session->get('activeRoom');
            $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);
        }
        else {
            $userRoomConnection = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser()]);
            $room = $userRoomConnection->getRoom();
        }

        $session->set('activeQuestion', $room->getCurrentQuestionNumber());
        $session->set('isShown', $room->getIsShown());

        if ($room->getStatus() != 1) {
            return $this->redirectToRoute('room');
        }

        if ($room->getHost() == $this->getUser()) {
            $admin = true;
        } else {
            $admin = false;
        }

        if ($admin) {
            if (isset($_GET['nextRound'])) {
                $room->setIsShown(0);

                if ($room->getCurrentQuestionNumber() < $room->getNumberOfQuestions())
                    $room->setCurrentQuestionNumber($room->getCurrentQuestionNumber()+1);
                else {
                    $room->setStatus('o');
                }

                $entityManager->persist($room);
                $entityManager->flush();
                return $this->redirectToRoute('game');
            } else if (isset($_GET['previousRound'])) {
                $room->setIsShown(1);
                
                if ($room->getCurrentQuestionNumber() > 1)
                    $room->setCurrentQuestionNumber($room->getCurrentQuestionNumber()-1);

                $entityManager->persist($room);
                $entityManager->flush();
                return $this->redirectToRoute('game');
            } else if (isset($_GET['showResult'])) {
                $room->setIsShown(1);
                $entityManager->persist($room);
                $entityManager->flush();
                return $this->redirectToRoute('game');
            } else if (isset($_GET['showGame'])) {
                $room->setIsShown(0);
                $entityManager->persist($room);
                $entityManager->flush();
                return $this->redirectToRoute('game');
            } else if (isset($_GET['theEnd'])) {
                $room->setStatus('o');
                $entityManager->persist($room);
                $entityManager->flush();
                return $this->redirectToRoute('room');
            }
        }

        $questionNumber = $room->getCurrentQuestionNumber();
        $question = $entityManager->getRepository(RoomQuestion::class)->findOneBy(['room' => $room, 'question_number' => $questionNumber]);
        $questionContent = $question->getQuestion()->getContent();

        $userAnswer = $entityManager->getRepository(Answer::class)->findOneBy(['room_question' => $question, 'user' => $this->getUser()]);
        $usersAnswer = $entityManager->getRepository(Answer::class)->findBy(['room_question' => $question]);

        if ($userAnswer) {
            $confirm = true;
        }

        $answerNumber = count($usersAnswer);

        $answer = new Answer();

        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($userAnswer) {
                $this->addFlash('warning', 'Odpowiedź na to pytanie już została przez Ciebie udzielona!');
            } else {
                $confirm = true;
                $answer->setRoomQuestion($question);
                $answer->setUser($this->getUser());

                $userAns = $entityManager->getRepository(User::class)->findOneBy(['id' => $form->get('answer')->getData()]);
                $userRoomConnection = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $userAns, 'room' => $room]);
                $answer->setAnswer($userRoomConnection);

                $entityManager->persist($answer);
                $entityManager->flush();
            }
        }

        if ($room->getIsShown() == 1) {
            $type = 'result';

            $counter = 0;

            foreach ($usersAnswer as $ur) {
                $find = false;

                for ($i=0; $i<$counter; $i++) {
                    if ($table[$i][0] == $ur->getAnswer()->getUser()->getName()) {
                        $find = true;
                        break;
                    }
                }

                if ($find) {
                    $table[$i][1]++;
                } else {
                    $table[$counter][0] = $ur->getAnswer()->getUser()->getName();
                    $table[$counter][1] = 1;
                    $counter++;
                }
            }

            if ($answerNumber == 0) {
                $this->addFlash('warning', 'Nikt nie udzielił odpowiedzi!');
            }

        } else
            $type = 'form';

        return $this->render('game/index.html.twig', [
            'answer' => $form->createView(),
            'question' => $questionContent,
            'confirm' => $confirm,
            'isAdmin' => $admin,
            'type' => $type,
            'table' => $table,
            'sum' => $answerNumber,
            'round' => $room->getCurrentQuestionNumber(),
            'allRound' => $room->getNumberOfQuestions()
        ]);
    }

    /**
     * @Route("/getState", name="getState")
     */
    public function getState(SessionInterface $session)
    {
        sleep(1);

        $entityManager = $this->getDoctrine()->getManager();

        if ($session->get('activeRoom')) {
            $roomNumber = $session->get('activeRoom');
            $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);
        }

        $state = 0;

        if ($room->getStatus() != 1)
            $state = 1;

        if ($session->get('activeQuestion')) {
            if ($session->get('activeQuestion') != $room->getCurrentQuestionNumber()) {
                $state = 1;
                $session->set('activeQuestion', $room->getCurrentQuestionNumber());
            }
        }

        if ($session->get('isShown') != $room->getIsShown()) {
            $state = 1;
            $session->set('isShown', $room->getIsShown());
        }

        return new Response($state);
    }
}