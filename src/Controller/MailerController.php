<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;;
 
class MailerController extends AbstractController
{
    #[Route('/send-email', name: 'app_send_email')]
    public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('sample-sender@binaryboxtuts.com')
            ->to('attia.yefa.attia@gmail.com')
            ->subject('Karhabt-e ')
            ->text('Bonjour cher client , votre dossier est finalement validé , contactez nous pour plus d information ');
 
        $mailer->send($email);
       

        return $this->redirectToRoute('app_dossier_back_index', [], Response::HTTP_SEE_OTHER);
}
}
