<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Room;
use App\Entity\RoomConnection;
use App\Entity\RoomQuestion;
use App\Entity\User;
use App\Form\AddQuestionType;
use App\Form\InviteUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RoomController extends AbstractController
{
    /**
     * @Route("/room", name="room")
     */
    public function index(Request $request, SessionInterface $session): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('app_login');

        $entityManager = $this->getDoctrine()->getManager();

        $roomActual = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser()]);

        if ($roomActual) {
            $session->set('activeRoom', $roomActual->getRoom()->getId());
        }

        $newRoomConnection = new RoomConnection();

        $form = $this->createForm(InviteUserType::class, $newRoomConnection);
        $form->handleRequest($request);

        $addRoomQuestion = new RoomQuestion();

        $form2 = $this->createForm(AddQuestionType::class, $addRoomQuestion);
        $form2->handleRequest($request);

        $roomNumber = $session->get('activeRoom');

        $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);

        if (isset($_GET['leaveRoom'])) {
            $roomExit = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $room, 'user' => $this->getUser()]);

            if ($roomExit) {
                $entityManager->remove($roomExit);
                $entityManager->flush();
                $session->remove('activeRoom');
            }
        }

        if (isset($_GET['closeRoom'])) {
            $roomClose = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

            if ($roomClose) {
                $roomExit = $entityManager->getRepository(RoomConnection::class)->findBy(['room' => $roomClose]);

                foreach ($roomExit as $r) {
                    $entityManager->remove($r);
                    $entityManager->flush();
                }

                $roomClose->setStatus('r');
                $entityManager->persist($roomClose);
                $entityManager->flush();

                $session->remove('activeRoom');
            }
        }

        if (isset($_GET['deleteQuestion'])) {
            $roomClose = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

            if ($roomClose) {
                $question = $entityManager->getRepository(Question::class)->findOneBy(['id' => $_GET['deleteQuestion']]);
                $deleteQuestion = $entityManager->getRepository(RoomQuestion::class)->findOneBy(['room' => $roomClose, 'question' => $question]);

                if ($deleteQuestion) {
                    $entityManager->remove($deleteQuestion);
                    $entityManager->flush();

                    $num = $room->getNumberOfQuestions()-1;
                    $room->setNumberOfQuestions($num);

                    $entityManager->persist($room);
                    $entityManager->flush();
                }
            }
        }

        if (isset($_GET['startGame'])) {
            $roomClose = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

            if ($roomClose) {
                $roomClose->setStatus('1');
                $roomClose->setCurrentQuestionNumber(1);
                $entityManager->persist($roomClose);
                $entityManager->flush();

                return $this->redirectToRoute('game');
            }
        }

        if (isset($_GET['deleteUser'])) {
            $roomClose = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

            if ($roomClose) {
                $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $_GET['deleteUser']]);
                $deleteUser = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $roomClose, 'user' => $user]);

                if ($deleteUser) {
                    $entityManager->remove($deleteUser);
                    $entityManager->flush();
                }
            }
        }
    
        if (!$roomNumber) {
        
            return $this->redirectToRoute('index');

        } else {
            $userVerification3 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser(), 'room' => $room]);

            if (!$userVerification3) {
                return $this->redirectToRoute('index');
            } else {
                if ($room->getStatus() == 1) {
                    return $this->redirectToRoute('game');
                }
            }
        }

        $admin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);
        $codeNumber = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);

        if ($form->isSubmitted() && $form->isValid()) {
            $userVerification = $entityManager->getRepository(User::class)->findOneBy(['code' => $form->get('code')->getData()]);
            $userVerification2 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $userVerification, 'room' => $room]);

            if ($userVerification && !$userVerification2) {
                $newRoomConnection->setRoom($room);
                $newRoomConnection->setUser($userVerification);
                $newRoomConnection->setIsAccepted(1);

                $entityManager->persist($newRoomConnection);
                $entityManager->flush();
            } else {
                if (!$userVerification)
                    $this->addFlash('warning', 'Podany kod jest niepoprawny!');
                else
                    $this->addFlash('warning', 'Podana osoba jest już obecna w pokoju!');
            }
        }

        if ($form2->isSubmitted() && $form2->isValid()) {
            if ($admin) {
                $question = $entityManager->getRepository(Question::class)->findOneBy(['id' => $form2->get('question')->getData()]);
                $questionNumbers = $entityManager->getRepository(RoomQuestion::class)->findBy(['room' => $codeNumber]);
                $questionValidate = $entityManager->getRepository(RoomQuestion::class)->findOneBy(['room' => $codeNumber, 'question' => $question]);

                if (!$questionValidate) {
                    $counter = 1;

                    foreach ($questionNumbers as $qn) {
                        $counter++;
                    }
    
                    $addRoomQuestion->setRoom($codeNumber);
                    $addRoomQuestion->setQuestion($question);
                    $addRoomQuestion->setQuestionNumber($counter);
    
                    $entityManager->persist($addRoomQuestion);
                    $entityManager->flush();

                    $num = $room->getNumberOfQuestions()+1;
                    $room->setNumberOfQuestions($num);

                    $entityManager->persist($room);
                    $entityManager->flush();
                } else {
                    $this->addFlash('warning', 'Wybrane pytanie jest już dodane');
                }
            }
        }

        $roomPlayers = $entityManager->getRepository(RoomConnection::class)->findBy(['room' => $roomNumber]);
        $players = null;

        foreach ($roomPlayers as $rp) {
            $isAdmin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $rp->getUser()]);

            if ($isAdmin)
                $players[] = [
                    'name' => $rp->getUser()->getName() . ' (ADMIN)',
                    'id' => $rp->getUser()->getId(),
                    'admin' => true
                ];
            else
                $players[] = [
                    'name' => $rp->getUser()->getName(),
                    'id' => $rp->getUser()->getId(),
                    'admin' => false
                ];
        }

        $questionList = $entityManager->getRepository(RoomQuestion::class)->findBy(['room' => $roomNumber]);
        $questions = null;

        foreach ($questionList as $q) {
            $questions[] = [
                'content' => $q->getQuestion()->getContent(),
                'id' => $q->getQuestion()->getId()
            ];
        }

        unset($newRoomConnection);
        unset($form);

        $newRoomConnection = new RoomConnection();
        $form = $this->createForm(InviteUserType::class, $newRoomConnection);

        unset($addRoomQuestion);
        unset($form2);

        $addRoomQuestion = new RoomQuestion();
        $form2 = $this->createForm(AddQuestionType::class, $addRoomQuestion);

        return $this->render('room/index.html.twig', [
            'players' => $players,
            'questions' => $questions,
            'newRoomConnection' => $form->createView(),
            'addRoomQuestion' => $form2->createView(),
            'isAdmin' => $admin,
            'code' => $codeNumber->getCode(),
        ]);
    }

    /**
     * @Route("/userList", name="userList")
     */
    public function userList(SessionInterface $session)
    {
        sleep(1);

        $entityManager = $this->getDoctrine()->getManager();

        $roomNumber = $session->get('activeRoom');
        $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);

        $roomPlayers = $entityManager->getRepository(RoomConnection::class)->findBy(['room' => $room]);
        $players = null;

        $isAdminAll = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

        if ($isAdminAll) {
            $players[] = [
                'name' => 'ADMIN',
                'id' => $this->getUser()->getId(),
                'admin' => true
            ];
        } else {
            $players[] = [
                'name' => 'NO ADMIN',
                'id' => $this->getUser()->getId(),
                'admin' => false
            ];
        }

        foreach ($roomPlayers as $rp) {
            $isAdmin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $rp->getUser()]);

            if ($isAdmin)
                $players[] = [
                    'name' => $rp->getUser()->getName() . ' (ADMIN)',
                    'id' => $rp->getUser()->getId(),
                    'admin' => true
                ];
            else
                $players[] = [
                    'name' => $rp->getUser()->getName(),
                    'id' => $rp->getUser()->getId(),
                    'admin' => false
                ];
        }

        return new Response(json_encode($players));
    }

    /**
     * @Route("/questionList", name="questionList")
     */
    public function questionList(SessionInterface $session)
    {
        sleep(1);

        $entityManager = $this->getDoctrine()->getManager();

        $roomNumber = $session->get('activeRoom');
        $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);

        $roomQuestions = $entityManager->getRepository(RoomQuestion::class)->findBy(['room' => $room]);
        $questions = null;

        $isAdminAll = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

        if ($isAdminAll) {
            $questions[] = [
                'content' => true,
                'id' => 0,
            ];
        } else {
            $questions[] = [
                'content' => false,
                'id' => 0,
            ];
        }

        foreach ($roomQuestions as $rq) {
            $questions[] = [
                'content' => $rq->getQuestion()->getContent(),
                'id' => $rq->getQuestion()->getId()
            ];
        }

        return new Response(json_encode($questions));
    }

    /**
     * @Route("/getMe", name="getMe")
     */
    public function getMe(SessionInterface $session)
    {
        sleep(1);

        $entityManager = $this->getDoctrine()->getManager();

        $roomNumber = $session->get('activeRoom');
        $room = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber]);

        $roomQuestions = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $room, 'user' => $this->getUser()]);
        $return = null;
        
        if ($roomQuestions)
            $return['in'] = 1;
        else
            $return['in'] = 0;

        if ($room->getStatus() == 1)
            $return['out'] = 1;
        else
            $return['out'] = 0;

        return new Response(json_encode($return));
    }
}
