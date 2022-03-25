<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{



    #[Route('/contact', name: 'contact', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('/contact.html.twig', []);
    }



    #[Route('/email', name: 'email', methods: ['POST'])]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {

       $message =  $request->request->get('message');
       $email = $request->request->get('email');
       $name =  $request->request->get('name');
       $subject = $request->request->get('subject');

        $email = (new Email())
        ->from('charles@emancipateur.com')
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
       
            ->html('<p>'.$message.'</p><p>'.$name.'</p>');

        $mailer->send($email);
        $this->addFlash(
            'warning',
            'Votre email à été envoyé!'
        );
        return $this->redirectToRoute('contact',[], Response::HTTP_SEE_OTHER);
    }
    
}