<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="settings")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('index');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());
        $form = $this->createForm(SettingsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return $this->render('settings/settings.html.twig', [
            'settingsForm' => $form->createView(),
            'user' => $user
        ]);
    }
}