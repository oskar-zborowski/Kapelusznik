<?php

namespace App\Controller;

use App\Entity\Agreement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AgreementController extends AbstractController
{
    /**
     * @Route("/agreement/{agreement}", name="agreement")
     */
    public function index(string $agreement): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $agreement = $entityManager->getRepository(Agreement::class)->findOneBy(['content' => $agreement]);

        return $this->render('agreement/index.html.twig', [
            'agreement' => 'agreement/' . $agreement->getContent(),
            'title' => $agreement->getName(),
        ]);
    }
}
