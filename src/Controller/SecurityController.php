<?php

namespace App\Controller;

use App\Entity\UserActivity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/fakeExit", name="app_fakeExit")
     */
    public function fakeExit(SessionInterface $session)
    {
        if ($this->getUser()) {
            do
                $session->set('userSessionCodePattern', mt_rand());
            while ($session->get('userSessionCodePattern') == $session->get('userSessionCode'));

            $user = $this->getUser();
            $user->setIsLoggedIn(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $actualDate = new \DateTime();
            $session->set('userSessionRememberedDate', $actualDate->format('Y-m-d H:i:s'));
        }

        return new Response();
    }

    /**
     * @Route("/delayExit", name="app_delayExit")
     */
    public function delayExit(SessionInterface $session)
    {
        if ($this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($session->get('userSessionCodePattern') && $session->get('userSessionCode'));
            else {
                $session->set('userSessionCodePattern', mt_rand());

                do
                    $session->set('userSessionCode', mt_rand());
                while ($session->get('userSessionCode') == $session->get('userSessionCodePattern'));
            }

            if ($session->get('userSessionCode') != $session->get('userSessionCodePattern')) {
                $session->set('userSessionCode', $session->get('userSessionCodePattern'));

                $user = $this->getUser();
                $user->setIsLoggedIn(1);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            if ($session->get('userSessionRememberedDate'));
            else {
                $actualDate = new \DateTime();
                $session->set('userSessionRememberedDate', $actualDate->format('Y-m-d H:i:s'));
            }

            $year = (int)($session->get('userSessionRememberedDate')[0] . $session->get('userSessionRememberedDate')[1] . $session->get('userSessionRememberedDate')[2] . $session->get('userSessionRememberedDate')[3]);
            $month = (int)($session->get('userSessionRememberedDate')[5] . $session->get('userSessionRememberedDate')[6]);
            $day = (int)($session->get('userSessionRememberedDate')[8] . $session->get('userSessionRememberedDate')[9]);
            $hour = (int)($session->get('userSessionRememberedDate')[11] . $session->get('userSessionRememberedDate')[12]);
            $minute = (int)($session->get('userSessionRememberedDate')[14] . $session->get('userSessionRememberedDate')[15]);
            $second = (int)($session->get('userSessionRememberedDate')[17] . $session->get('userSessionRememberedDate')[18]);

            $dateRemembered = mktime($hour, $minute, $second, $month, $day, $year);
            $actualDate = time();
            
            if ($session->get('delayExitId') && ($actualDate <= $dateRemembered + 30)) {
                $userActivity = $entityManager->getRepository(UserActivity::class)->findOneBy(
                    ['id' => $session->get('delayExitId')]);
                $userActivity->setDate(new \DateTime());

                $entityManager->persist($userActivity);
                $entityManager->flush();
            } else {
                $userActivity[0] = new UserActivity();
                $userActivity[0]->setUser($this->getUser());
                $userActivity[0]->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity[0]->setActivity('LOGGED_IN');
                $userActivity[0]->setDate(new \DateTime());

                $userActivity[1] = new UserActivity();
                $userActivity[1]->setUser($this->getUser());
                $userActivity[1]->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity[1]->setActivity('LOGGED_OUT');
                $userActivity[1]->setDate(new \DateTime());

                $entityManager->persist($userActivity[0]);
                $entityManager->persist($userActivity[1]);
                $entityManager->flush();

                $session->set('delayExitId', $userActivity[1]->getId());
            }

            $actualDate = new \DateTime();
            $session->set('userSessionRememberedDate', $actualDate->format('Y-m-d H:i:s'));
            $response = 1;
        } else {
            $session->set('delayExitId', 0);
            $response = 0;
        }

        return new Response($response);
    }
}