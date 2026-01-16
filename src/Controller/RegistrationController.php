<?php

namespace App\Controller;

use App\Repository\RoleRepository;
use App\Entity\Employer;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }


    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        RoleRepository $roleRepository
    ): Response
    {
        // 1. On ne crée plus l'objet $user ici, car on ne sait pas encore si c'est un candidat ou un employeur
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 2. On récupère le choix de l'utilisateur
            $userType = $form->get('user_type')->getData();

            // 3. On instancie la BONNE classe selon le choix (Héritage JOINED)
            if ($userType === 'candidate') {
                $user = new \App\Entity\Candidate();
            } else {
                $user = new Employer();
            }

            // 4. On hydrate l'utilisateur avec les données communes
            $user->setEmail($form->get('email')->getData());
            
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // --- GESTION DU RÔLE ---
            // On cherche dans ta table 'role' l'entrée qui a pour name_role 'ROLE_USER'
            $defaultRole = $roleRepository->findOneBy(['name_role' => 'ROLE_USER']);

            if($defaultRole) {
                $user->setRole($defaultRole);
            }else {
                // En cas d'erreur (la table Role est vide ou le rôle n'existe pas)
                throw new \Exception("Le rôle ROLE_USER n'existe pas en base de données. Créez-le d'abord !");
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // 5. L'envoi de l'email reste identique
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact.clean.conect@gmail.com', 'Clean connect'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Message pour prévenir l'utilisateur
            $this->addFlash('success', 'Inscription réussie ! Veuillez vérifier votre boîte mail pour confirmer votre compte.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), // Ajoute .createView() c'est plus propre
        ]);
    }
    

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
