<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyQuestionController extends AbstractController
{
    /**
     * @Route("/my_question", name="my_question")
     */
    public function index(): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('index');

        $entityManager = $this->getDoctrine()->getManager();

        if (isset($_GET['deleteQuestion'])) {
            $question = $entityManager->getRepository(Question::class)->findOneBy(['id' => $_GET['deleteQuestion'], 'creator' => $this->getUser()]);

            if ($question) {
                $trash = $entityManager->getRepository(User::class)->findOneBy(['id' => 3]);
                $question->setCreator($trash);
                $entityManager->persist($question);
                $entityManager->flush();
            }
        }

        $questions = $entityManager->getRepository(Question::class)->findBy(['creator' => $this->getUser()]);

        $response = null;
        $category = null;
        $available['OT'] = false;
        $available['JBT'] = false;
        $available['MMW'] = false;
        $available['18'] = false;

        foreach ($questions as $q) {

            if ($q->getCategory() == 'OT') {
                $category = 'Życiowe tematy - pytania ogólne';
            } else if ($q->getCategory() == 'JBT') {
                $category = 'Jazda bez trzymanki - najbardziej niezręczne pytania';
            } else if ($q->getCategory() == 'MMW') {
                $category = 'Mów mi więcej - słodzenie sobie';
            } else if ($q->getCategory() == '18') {
                $category = '18+ - kontekst erotyczny';
            }

            if ($q->getCategory() == 'OT') {
                $available['OT'] = true;

                $response['OT'][] = [
                    'id' => $q->getId(),
                    'content' => $q->getContent(),
                    'category' => $category,
                    'date_added' => date_format($q->getDateAdded(), 'd.m.Y'),
                ];
            } else if ($q->getCategory() == 'JBT') {
                $available['JBT'] = true;

                $response['JBT'][] = [
                    'id' => $q->getId(),
                    'content' => $q->getContent(),
                    'category' => $category,
                    'date_added' => date_format($q->getDateAdded(), 'd.m.Y'),
                ];
            } else if ($q->getCategory() == 'MMW') {
                $available['MMW'] = true;

                $response['MMW'][] = [
                    'id' => $q->getId(),
                    'content' => $q->getContent(),
                    'category' => $category,
                    'date_added' => date_format($q->getDateAdded(), 'd.m.Y'),
                ];
            } else if ($q->getCategory() == '18') {
                $available['18'] = true;

                $response['18'][] = [
                    'id' => $q->getId(),
                    'content' => $q->getContent(),
                    'category' => $category,
                    'date_added' => date_format($q->getDateAdded(), 'd.m.Y'),
                ];
            }
        }

        return $this->render('my_question/index.html.twig', [
            'questions' => $response,
            'available' => $available
        ]);
    }
}
