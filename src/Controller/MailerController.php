<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
class MailerController extends AbstractController
{
    #[Route('/send-email/{idu}', name: 'app_send_email')]
    public function index($idu, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        // Retrieve the user's email address using the provided idu
        $user = $userRepository->find($idu);

        // Ensure that the user is found and has an email address
        if ($user && $user->getEmail()) {
            // Retrieve the user's email address
            $emailAddress = $user->getEmail();

            // Create the email message
            $email = (new Email())
                ->from('sample-sender@binaryboxtuts.com')
                ->to($emailAddress)
                ->subject('Karhabt-e ')
                ->text('Bonjour cher client, votre dossier est finalement validé, contactez-nous pour plus d\'information');

            // Send the email
            $mailer->send($email);
        }

        return $this->redirectToRoute('app_dossier_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
