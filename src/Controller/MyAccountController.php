<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserActivity;
use App\Entity\UserAgreement;
use App\Form\BasicUserDataType;
use App\Form\ChangePasswordFormType;
use App\Service\AgreementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MyAccountController extends AbstractController
{
    /**
     * @Route("/my_account", name="my_account")
     */
    public function index(Request $request, AgreementService $agreementService, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('index');

        $emailError = NULL;

        $agreement = $agreementService->findCurrentAgreements(false);
        $agr = null;

        foreach ($agreement as $a) {
            $agr['agr' . $a->getId()]['content'] = $a->getContent();
            $agr['agr' . $a->getId()]['name'] = $a->getName();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $this->getUser()]);

        $lastPicture = $user->getProfilePicture();

        $form = $this->createForm(BasicUserDataType::class);
        $form->handleRequest($request);

        $form2 = $this->createForm(ChangePasswordFormType::class);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('name')->getData() != $user->getName()) {
                $user->setName($form->get('name')->getData());

                $userActivity = new UserActivity();
                $userActivity->setUser($this->getUser());
                $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity->setActivity('NAME_CHANGE');
                $userActivity->setDate(new \DateTime());

                $entityManager->persist($userActivity);
                $entityManager->flush();
            }

            if ($form->get('date_of_birth')->getData() != $user->getDateOfBirth()) {
                $user->setDateOfBirth($form->get('date_of_birth')->getData());

                $userActivity = new UserActivity();
                $userActivity->setUser($this->getUser());
                $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity->setActivity('DATE_OF_BIRTH_CHANGE');
                $userActivity->setDate(new \DateTime());

                $entityManager->persist($userActivity);
                $entityManager->flush();
            }

            if ($form->get('gender')->getData() != $user->getGender()) {
                $user->setGender($form->get('gender')->getData());

                $userActivity = new UserActivity();
                $userActivity->setUser($this->getUser());
                $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity->setActivity('GENDER_CHANGE');
                $userActivity->setDate(new \DateTime());

                $entityManager->persist($userActivity);
                $entityManager->flush();
            }

            if ($form->get('email')->getData() != $user->getEmail()) {
                if (!$entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()])) {
                    $user->setEmail($form->get('email')->getData());
    
                    $userActivity = new UserActivity();
                    $userActivity->setUser($this->getUser());
                    $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                    $userActivity->setActivity('EMAIL_CHANGE');
                    $userActivity->setDate(new \DateTime());
    
                    $entityManager->persist($userActivity);
                    $entityManager->flush();
                }
                else {
                    $emailError = 'Podany adres e-mail jest już w użyciu';
                }
            }

            if ($form->get('active_login_form')->getData() != $user->getActiveLoginForm()) {
                $user->setActiveLoginForm($form->get('active_login_form')->getData());

                $userActivity = new UserActivity();
                $userActivity->setUser($this->getUser());
                $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                $userActivity->setActivity('ACTIVE_LOGIN_FORM_CHANGE');
                $userActivity->setDate(new \DateTime());

                $entityManager->persist($userActivity);
                $entityManager->flush();
            }

            $entityManager->persist($user);
            $entityManager->flush();

            foreach ($agreement as $a) {
                $found = false;
                $agreementId = NULL;
                $userAgreement = $entityManager->getRepository(UserAgreement::class)->findBy(['user' => $this->getUser()]);

                foreach ($userAgreement as $ua) {
                    if ($ua->getAgreement()->getSignature() == $a->getSignature() && $ua->getCancellationDate() == NULL) {
                        $found = true;
                        $agreementId = $ua->getId();
                        break;
                    }
                }

                if ($form->get('agr' . $a->getId())->getData()) {
                    if (!$found) {
                        $userAgreement = new UserAgreement;
                        $userAgreement->setUser($user);
                        $userAgreement->setAgreement($a);
                        $userAgreement->setDateOfAccepting(new \DateTime());
        
                        $entityManager->persist($userAgreement);
                        $entityManager->flush();
                    }
                } else {
                    if ($found) {
                        $userAgreement = $entityManager->getRepository(UserAgreement::class)->findOneBy(['id' => $agreementId]);
                        $userAgreement->setCancellationDate(new \DateTime());
        
                        $entityManager->persist($userAgreement);
                        $entityManager->flush();
                    }
                }
            }

            $profilePicture = $form->get('profile_picture')->getData();

            if ($profilePicture) {
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

                        $filename .= '.' . $profilePicture->guessExtension();
        
                        $filenameVerification = $entityManager->getRepository(User::class)->findOneBy(['profile_picture' => $filename]);
                    } while ($filenameVerification);

                    $profilePicture->move('images', $filename);

                    $user->setProfilePicture($filename);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    if ($lastPicture != 'unk.jpeg')
                        unlink('images/' . $lastPicture);
    
                    $userActivity = new UserActivity();
                    $userActivity->setUser($this->getUser());
                    $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
                    $userActivity->setActivity('PROFILE_PICTURE_CHANGE');
                    $userActivity->setDate(new \DateTime());
    
                    $entityManager->persist($userActivity);
                    $entityManager->flush();
                    
                    $this->addFlash('success', 'Ustawiono nowe zdjęcie profilowe!');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Wystąpił nieoczekiwany błąd!');
                }
            }

            return $this->redirectToRoute('my_account');
        }

        if ($form2->isSubmitted() && $form2->isValid()) {
            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form2->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);

            $userActivity = new UserActivity();
            $userActivity->setUser($user);
            $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
            $userActivity->setActivity('PASSWORD_CHANGE_LOGGED_IN');
            $userActivity->setDate(new \DateTime());

            $entityManager->persist($userActivity);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('my_account');
        }

        if (isset($_GET['deleteAccount'])) {
            $user->setIsActive(0);
            $user->setIsLoggedIn(0);

            $entityManager->persist($user);
            $entityManager->flush();

            $logoutPath = $this->container->get('router')->generate('app_logout');
            $logoutRequest = Request::create($logoutPath);
            $logoutResponse = $this->container->get('http_kernel')->handle($logoutRequest);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('my_account/index.html.twig', [
            'basicUserDataForm' => $form->createView(),
            'resetForm' => $form2->createView(),
            'agr' => $agr,
            'email_error' => $emailError,
        ]);
    }
}
