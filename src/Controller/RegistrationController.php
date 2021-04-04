<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $passwordConfirmationError = NULL;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData() != $form->get('confirmationPassword')->getData()) {
                $passwordConfirmationError = 'Podane hasła różnią się';
            } else {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $code = NULL;
                $entityManager = $this->getDoctrine()->getManager();

                do {
                    for ($i=0; $i<6; $i++)
                    {
                        $rand = rand(0, 35);
                    
                        if ($rand < 10)
                            $code .= chr($rand+48);
                        else
                            $code .= chr($rand+55);
                    }

                    $codeVerification = $entityManager->getRepository(User::class)->findOneBy(['code' => $code]);
                } while ($codeVerification);

                $user->setCode($code);
                $user->setRoles(["ROLE_USER"]);
                $user->setProfilePicture('unk.jpg');
                $user->setActiveLoginForm('s');
                $user->setDateOfJoining(new \DateTime());
                $user->setIsActive(1);
                $user->setIsBlocked(0);
                $user->setIsLoggedIn(0);
                $user->setIsVerified(0);

                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('biuro.prasowe.warszawa@gmail.com', 'Kapelusznik.pl'))
                        ->to($user->getEmail())
                        ->subject('Potwierdź swój adres e-mail')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                // do anything else you need here, like send an email

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'error' => $passwordConfirmationError
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Twój adres e-mail został zweryfikowany!');

        return $this->redirectToRoute('app_login');
    }
}
