<?php

namespace App\Controller;

use App\Form\UserProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile/complete', name: 'app_profile_complete')]
    #[IsGranted('ROLE_USER')] //il faut être connecté pour voir cette page
    public function complete(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Récupère l'user connecté
        $user = $this->getUser();

        //Création du formulaire
        $form = $this->createForm(UserProfilType::class, $user);
        $form->handleRequest($request);

        //Vérifier qu'on reçoit un formulaire + vérifier qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //Redirection
            $this->addFlash('success', 'Votre profil a été modifié ✅');
            return $this->redirectToRoute('app_home');
        }else {
            dump($form->getErrors(true, true));
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
