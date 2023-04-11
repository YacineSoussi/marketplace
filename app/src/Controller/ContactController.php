<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(MailerInterface $mailer, Request $request): Response
    {
        $contact = null;

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $contact = $form->getData();

            // dd($contact['email']);

            $email = (new TemplatedEmail())
            ->from($contact['email'])
            ->to("no-reply@moon-factory.fr")
            ->subject("Demande de contact : " . $contact['subject'])
            ->htmlTemplate('mail/contact.html.twig')
            ->context([
                'contact' => $contact
            ]);
            
        $mailer->send($email);
        $this->addFlash('message', 'Votre demande de contact a bien été effectuée');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
