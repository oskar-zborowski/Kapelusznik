<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\UserActivity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $user;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token))
            throw new InvalidCsrfTokenException();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error (wrong e-mail)
            throw new CustomUserMessageAuthenticationException('Nieprawidłowy e-mail lub hasło');
        } else
            $this->user = $user;

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        if (!$isPasswordValid) {
            // fail authentication with a custom error (wrong password)
            $userActivity = new UserActivity();
            $userActivity->setUser($this->user);
            $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
            $userActivity->setActivity('LOGGING_THROUGH_WRONG_PASSWORD');
            $userActivity->setDate(new \DateTime());
        
            $this->entityManager->persist($userActivity);
            $this->entityManager->flush();

            throw new CustomUserMessageAuthenticationException('Nieprawidłowy e-mail lub hasło');
        } else if ($this->user->getIsBlocked()) {
            // fail authentication with a custom error (blocked account)
            $userActivity = new UserActivity();
            $userActivity->setUser($this->user);
            $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
            $userActivity->setActivity('LOGGING_INTO_BLOCKED_ACCOUNT');
            $userActivity->setDate(new \DateTime());
        
            $this->entityManager->persist($userActivity);
            $this->entityManager->flush();

            throw new CustomUserMessageAuthenticationException('Twoje konto zostało zablokowane');
        } else if (!$this->user->isVerified()) {
            // fail authentication with a custom error (unverified e-mail)
            $userActivity = new UserActivity();
            $userActivity->setUser($this->user);
            $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
            $userActivity->setActivity('LOGGING_THROUGH_UNVERIFIED_EMAIL');
            $userActivity->setDate(new \DateTime());
        
            $this->entityManager->persist($userActivity);
            $this->entityManager->flush();

            throw new CustomUserMessageAuthenticationException('Twój adres e-mail nie został jeszcze potwierdzony');
        } else if (!$this->user->getIsActive()) {
            // fail authentication with a custom error (account deactivated)
            $userActivity = new UserActivity();
            $userActivity->setUser($this->user);
            $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
            $userActivity->setActivity('LOGGING_INTO_DEACTIVATED_ACCOUNT');
            $userActivity->setDate(new \DateTime());
        
            $this->entityManager->persist($userActivity);
            $this->entityManager->flush();

            throw new CustomUserMessageAuthenticationException('Twoje konto zostało przekazane do usunięcia');
        }

        return $isPasswordValid;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey))
            return new RedirectResponse($targetPath);

        // all other logs are located in SecurityController
        $userActivity = new UserActivity();
        $userActivity->setUser($this->user);
        $userActivity->setIpAddress($_SERVER['REMOTE_ADDR']);
        $userActivity->setActivity('AUTHENTICATION');
        $userActivity->setDate(new \DateTime());
    
        $this->entityManager->persist($userActivity);
        $this->entityManager->flush();

        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
