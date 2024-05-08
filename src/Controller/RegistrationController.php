<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twilio\Rest\Client;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageUser')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), 
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $user->setImageUser($newFilename);
            }

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['Client']);
            $user->setStatus( false );
            $entityManager->persist($user);
            $entityManager->flush();

            // Send welcome SMS
            $phoneNumberWithCountryCode = '+216' . $user->getNumTel();
            $this->sendWelcomeSms($phoneNumberWithCountryCode);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete_user')]
public function deleteUser(int $idu, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find($idu);

    // user mawjoud wale
    if (!$user) {
        $this->addFlash('danger', 'User not found.');
        return $this->redirectToRoute('app_user_index'); 
    }
    try {
        $entityManager->remove($user);
        $entityManager->flush();

        
        $this->addFlash('success', 'The user has been deleted successfully.');
    } catch (\Exception $e) {
        $this->addFlash('danger', 'There was a problem deleting the user.');
    }

    return $this->redirectToRoute('app_user_index'); 
}

private function sendWelcomeSms($phoneNumber)
    {
        $sid = ''; // Replace with your Twilio account SID
        $token = ''; // Replace with your Twilio auth token
        $twilio = new Client($sid, $token);
        $message = 'Bienvenu dans Karhabte\nTon compte a été crée avec succés.';

        try {
            $twilio->messages->create(
                $phoneNumber, // Phone number which receives the message
                [
                    'from' => '', // Replace with your Twilio phone number
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            // Handle errors here, e.g., log them or send an admin notification
        }
    }
}