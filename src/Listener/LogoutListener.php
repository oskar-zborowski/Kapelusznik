<?php

// namespace App\Listener;

// use App\Entity\User;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

// class LogoutListener implements LogoutHandlerInterface
// {
//     private $entityManager;
//     private $security;

//     public function __construct(EntityManagerInterface $entityManager, Security $security) {
//         $this->entityManager = $entityManager;
//         $this->security = $security;
//     }

//     public function logout(Request $Request, Response $Response, TokenInterface $Token) {
//         if ($this->security->getUser()) {
//             $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $this->security->getUser()]);
//             $user->setIsLoggedIn(0);
            
//             $this->entityManager->persist($user);
//             $this->entityManager->flush();
//         }
//     }
// }