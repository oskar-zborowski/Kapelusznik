<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomConnection;
use App\Form\InviteUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, SessionInterface $session): Response
    {
        $newRoomConnection = new RoomConnection();
        $form = $this->createForm(InviteUserType::class, $newRoomConnection);
        $form->handleRequest($request);

        if ($this->getUser()) {

            $entityManager = $this->getDoctrine()->getManager();

            if ($form->isSubmitted() && $form->isValid()) {
                $roomVerification = $entityManager->getRepository(Room::class)->findOneBy(['code' => $form->get('code')->getData()]);
                $roomVerification2 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $roomVerification, 'user' => $this->getUser()]);
    
                if ($roomVerification && !$roomVerification2) {

                    $roomExit = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser()]);

                    if ($roomExit) {
                        $entityManager->remove($roomExit);
                        $entityManager->flush();
                        $session->remove('activeRoom');
                    }

                    $newRoomConnection->setRoom($roomVerification);
                    $newRoomConnection->setUser($this->getUser());
                    $newRoomConnection->setIsAccepted(1);
    
                    $entityManager->persist($newRoomConnection);
                    $entityManager->flush();

                    $session->set('activeRoom', $newRoomConnection->getRoom()->getId());

                    return $this->redirectToRoute('room');

                } else {
                    if (!$roomVerification) {
                        $this->addFlash('warning', 'Podany kod jest niepoprawny!');
                    } else {
                        $session->set('activeRoom', $roomVerification->getId());
                        return $this->redirectToRoute('room');
                    }
                }

                unset($newRoomConnection);
                unset($form);

                $newRoomConnection = new RoomConnection();
                $form = $this->createForm(InviteUserType::class, $newRoomConnection);
            }
        }

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'newRoomConnection' => $form->createView(),
        ]);
    }
}
