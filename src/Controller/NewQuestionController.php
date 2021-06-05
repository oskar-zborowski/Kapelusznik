<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\NewQuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewQuestionController extends AbstractController
{
    /**
     * @Route("/new_question", name="new_question")
     */
    public function index(Request $request): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('index');

        $newQuestion = new Question();

        $form = $this->createForm(NewQuestionType::class, $newQuestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $checkNumberOfQuestion = $entityManager->getRepository(Question::class)->findBy(['creator' => $this->getUser()]);

            if (count($checkNumberOfQuestion) >= 30) {
                $this->addFlash('warning', 'Osiągnięto maksymalną liczbę 30 dodanych pytań');
            } else {
                $newQuestion->setCreator($this->getUser());
                $newQuestion->setGender('a');
                $newQuestion->setIsVerified(0);
                $newQuestion->setDateAdded(new \DateTime());
    
                $entityManager->persist($newQuestion);
                $entityManager->flush();
            }

            unset($newQuestion);
            unset($form);

            $newQuestion = new Question();
            $form = $this->createForm(NewQuestionType::class, $newQuestion);

            return $this->redirectToRoute('new_question');
        }
        
        return $this->render('new_question/index.html.twig', [
            'newQuestion' => $form->createView(),
        ]);
    }
}
