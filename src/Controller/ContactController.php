<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer, ContactRepository $repository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ip = $request->getClientIp();
            $contact->setIpAdress($ip);

            $name = $contact->getName();
            $lastname = $contact->getLastname();
            $email = $contact->getEmail();
            $object = $contact->getObject();
            $message = $contact->getMessage();

            $repository->save($contact, true);

            $email = (new Email())
                ->from(Address::create('Bedrinedev <no-reply@bedrinedev.fr>'))
                ->to('contact@bedrinedev.fr')
                ->replyTo($email)
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Contact - Bedrinedev')
                ->html('<p>Prénom Nom : ' . $name . ' ' . $lastname . '</p>
                <br><p>Email : ' . $email . '</p>
                <br><p>Objet : ' . $object . '</p>
                <br><p>Message : ' . nl2br($message) . '</p>');

            $mailer->send($email);

            $this->addFlash('notice', 'Message envoyé :) !');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
