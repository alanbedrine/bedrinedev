<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class AdminController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/admin', name: 'app_admin')]
    public function admin(ContactRepository $repository): Response
    {
        $contacts = $repository->findAll();

        return $this->render(
            'admin/index.html.twig',
            [
                'contacts' => $contacts
            ]
        );
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/email/{id}', name: 'app_admin_detail', requirements: ['id' => '\d+'])]
    public function detail(Contact $contact = null): Response
    {
        if (!$contact) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'admin/detail.html.twig',
            [
                'contact' => $contact
            ]
        );
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/suppr-email/{id}', methods: ['POST'], name: 'app_admin_deleted', requirements: ['id' => '\d+'])]
    public function deleted(Request $request, Contact $email = null, ContactRepository $repository): Response
    {
        if ($email) {
            $submittedToken = $request->request->get('token');
            $validToken = $this->isCsrfTokenValid('deleted', $submittedToken);
            if ($validToken) {
                $repository->remove($email, true);
            } else {
                throw $this->createNotFoundException();
            }
        } else {
            throw $this->createNotFoundException();
        }

        return $this->redirectToRoute('app_admin');
    }
}
