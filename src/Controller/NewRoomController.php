<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\User;
use App\Form\NewRoomType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewRoomController extends AbstractController
{
    /**
     * @Route("/new_room", name="new_room")
     */
    public function index(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('index');
        
        $newRoom = new Room();

        $form = $this->createForm(NewRoomType::class, $newRoom);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        $checkNumberOfRoom = $entityManager->getRepository(Room::class)->findBy(['host' => $this->getUser(), 'status' => 'o']);
        $checkNumberOfRoom2 = $entityManager->getRepository(Room::class)->findBy(['host' => $this->getUser(), 'status' => 'b']);
        $checkNumberOfRoom3 = $entityManager->getRepository(Room::class)->findBy(['host' => $this->getUser(), 'status' => 'c']);

        if (count($checkNumberOfRoom) >= 1 || count($checkNumberOfRoom2) >= 1 || count($checkNumberOfRoom3) >= 1) {
            return $this->redirectToRoute('room');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $code = NULL;

            do {
                for ($i=0; $i<6; $i++)
                {
                    $rand = rand(0, 35);
                
                    if ($rand < 10)
                        $code .= chr($rand+48);
                    else
                        $code .= chr($rand+55);
                }

                $codeVerification = $entityManager->getRepository(Room::class)->findOneBy(['code' => $code]);
            } while ($codeVerification);

            $newRoom->setCode($code);
            $newRoom->setHost($this->getUser());
            $newRoom->setStatus('o');

            $logoFilename = $form->get('logo_filename')->getData();

            if ($logoFilename) {
                try {
                    $filename = NULL;

                    do {
                        for ($i=0; $i<3; $i++)
                        {
                            $rand = rand(0, 61);
                        
                            if ($rand < 10)
                                $filename .= chr($rand+48);
                            else if ($rand < 36)
                                $filename .= chr($rand+55);
                            else
                                $filename .= chr($rand+61);
                        }

                        $filename .= '.' . $logoFilename->guessExtension();
        
                        $filenameVerification = $entityManager->getRepository(User::class)->findOneBy(['profile_picture' => $filename]);
                        $filenameVerification2 = $entityManager->getRepository(Room::class)->findOneBy(['logo_filename' => $filename]);
                    } while ($filenameVerification || $filenameVerification2);

                    $logoFilename->move('images', $filename);

                    $newRoom->setLogoFilename($filename);

                    $entityManager->persist($newRoom);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Ustawiono zdjęcie pokoju!');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Wystąpił nieoczekiwany błąd!');
                }
            } else {
                $newRoom->setLogoFilename('unk2.png');

                $entityManager->persist($newRoom);
                $entityManager->flush();
            }

            return $this->redirectToRoute('room');
        }

        return $this->render('new_room/index.html.twig', [
            'newRoom' => $form->createView(),
        ]);
    }
}
