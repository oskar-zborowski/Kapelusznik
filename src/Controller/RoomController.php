<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    /**
     * @Route("/room", name="room")
     */
    public function index(): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('app_login');

        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }
}
