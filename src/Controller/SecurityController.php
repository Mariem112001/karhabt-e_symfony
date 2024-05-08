<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface;
use  App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;



class SecurityController extends AbstractController
{
 
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security, EntityManagerInterface $entityManager): Response
    {
        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $security->getUser();
    
            // Check if the user is banned
            if ($user->isStatus()) {
                $this->addFlash('error', 'Your account has been banned.');
                return $this->redirectToRoute('app_logout');
            }
            
            if ($user->getRole() === "Admin") {
                return $this->redirectToRoute('app_user_index');
            } else {
                return $this->redirectToRoute('app_user_profile');
            }
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Check if the user is banned
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $lastUsername]);
        if ($user && $user->isStatus()) {
            $error = new CustomUserMessageAuthenticationException('Your account has been banned.');
        }
    
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


// reset password function (2)


   


}