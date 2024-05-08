<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\User1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


#[IsGranted('ROLE_ADMIN')]
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Admin/usersList.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_user_index');
        }

        try {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'There was an error deleting the user.');
        }

        return $this->redirectToRoute('app_user_index');
    }


    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
      // ken el auth user ynajm ychouf l profile mteou
        public function profile(): Response
        {
            $user = $this->getUser();  // yhez l user li dkhal

            if (!$user) {
                $this->addFlash('error', 'You need to be logged in to view the profile.');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('user/profile.html.twig', [
                'user' => $user,
            ]);
        }



        #[Route('/profile/save', name: 'save_profile', methods: ['POST'])]
        public function saveProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
        {
            $user = $this->getUser();
        
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            $user->setDateNaissance(new \DateTime($request->request->get('dateNaissance')));
            $user->setNumTel($request->request->get('numTel'));

    $imageFile = $request->files->get('imageUser');
    if ($imageFile) {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

        try {
            $imageFile->move(
                $this->getParameter('images_directory'), // hedha juste directory mtaa l images hatitou fel service.yaml
                $newFilename
            );

            $user->setImageUser($newFilename);
        } catch (FileException $e) {
            $this->addFlash('error', 'Failed to upload image.');
            return $this->redirectToRoute('app_user_profile');
        }
    }


        
            $entityManager->persist($user);
            $entityManager->flush();
        
            // Rediriger l'utilisateur vers la page du profil avec un message de succès
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_user_profile');
        }


        #[Route('/user/ban/{id}', name: 'user_ban', methods: ['POST'])]
        public function banUser(EntityManagerInterface $entityManager, $id): Response
        {
            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                $this->addFlash('error', 'User not found.');
            } else {
                $user->setStatus(true); // Ban the user
                $entityManager->flush();
                $this->addFlash('success', 'User has been banned successfully.');
            }

            return $this->redirectToRoute('app_user_index');
        }

        #[Route('/user/unban/{id}', name: 'user_unban', methods: ['POST'])]
            public function unbanUser(EntityManagerInterface $entityManager, $id): Response
            {
                $user = $entityManager->getRepository(User::class)->find($id);

                if (!$user) {
                    $this->addFlash('error', 'User not found.');
                } else {
                    $user->setStatus(false); // Unban the user
                    $entityManager->flush();
                    $this->addFlash('success', 'User has been unbanned successfully.');
                }

                return $this->redirectToRoute('app_user_index');
            }
}
