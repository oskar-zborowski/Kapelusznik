<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomConnection;
use App\Entity\User;
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

        $newRoomConnection = new RoomConnection();

        $form = $this->createForm(InviteUserType::class, $newRoomConnection);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

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
    
        if (!$roomNumber) {
        
            return $this->redirectToRoute('index');

        } else {
            $userVerification3 = $entityManager->getRepository(RoomConnection::class)->findOneBy(['user' => $this->getUser(), 'room' => $room]);

            if (!$userVerification3) {
                return $this->redirectToRoute('index');
            }
        }

        $admin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $this->getUser()]);

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

        $roomPlayers = $entityManager->getRepository(RoomConnection::class)->findBy(['room' => $roomNumber]);
        $players = null;

        foreach ($roomPlayers as $rp) {
            $isAdmin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $rp->getUser()]);

            if ($isAdmin)
                $players[] = $rp->getUser()->getName() . ' (ADMIN)';
            else
                $players[] = $rp->getUser()->getName();
        }

        unset($newRoomConnection);
        unset($form);

        $newRoomConnection = new RoomConnection();
        $form = $this->createForm(InviteUserType::class, $newRoomConnection);

        return $this->render('room/index.html.twig', [
            'players' => $players,
            'newRoomConnection' => $form->createView(),
            'isAdmin' => $admin,
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

        $roomPlayers = $entityManager->getRepository(RoomConnection::class)->findBy(['room' => $roomNumber]);
        $players = null;

        foreach ($roomPlayers as $rp) {
            $isAdmin = $entityManager->getRepository(Room::class)->findOneBy(['id' => $roomNumber, 'host' => $rp->getUser()]);

            if ($isAdmin)
                $players[] = $rp->getUser()->getName() . ' (ADMIN)';
            else
                $players[] = $rp->getUser()->getName();
        }

        return new Response(json_encode($players));
    }
}
