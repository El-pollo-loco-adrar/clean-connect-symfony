<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Employer;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        //si pas connecté, retour login
        if(!$user) {
            return $this->redirectToRoute('app_login');
        }

        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('admin');
        }

        if($user instanceof Employer) {
            return $this->render('home/employer_dashboard.html.twig', [
                'employer'=> $user
            ]);
        }
        if($user instanceof Candidate) {
            return $this->render('home/candidate_dashboard.html.twig', [
            'candidate'=> $user
            ]);
        }
        return $this->render('home/index.html.twig');
    }
}
