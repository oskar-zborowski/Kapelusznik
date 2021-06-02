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
                $roomVerification = $entityManager->getRepository(Room::class)->findOneBy(['code' => $form->get('code')->getData(), 'status' => 'o']);
                $roomVerification3 = $entityManager->getRepository(Room::class)->findOneBy(['code' => $form->get('code')->getData(), 'status' => '1']);
                $roomVerification2 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $roomVerification, 'user' => $this->getUser()]);
                $roomVerification4 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['room' => $roomVerification3, 'user' => $this->getUser()]);
    
                if (($roomVerification || $roomVerification3) && !$roomVerification2 && !$roomVerification4) {

                    $roomExit = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser()]);

                    if ($roomVerification3 && $roomVerification3->getStatus() == 1) {
                        $this->addFlash('warning', 'Pokój już wystartował i nie ma możliwości dołączenia!');
                    } else {
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
                    }

                } else {
                    if (!$roomVerification && !$roomVerification3) {
                        $this->addFlash('warning', 'Podany kod jest niepoprawny lub pokój został zamknięty!');
                    } else {
                        if ($roomVerification)
                            $session->set('activeRoom', $roomVerification->getId());
                        else if ($roomVerification3)
                            $session->set('activeRoom', $roomVerification3->getId());

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
