<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MissionListingController extends AbstractController
{
    #[Route('/candidate/missions', name: 'app_candidate_missions_available')]
    public function index(MissionRepository $missionRepository): Response
    {
        $user = $this->getUser();

        if(!$user instanceof Candidate) {
            return $this->redirectToRoute('app_login');
        }

        // On récupère les missions filtrées par zone
        $missions = $missionRepository->findMissionsByCandidateAreas($user);

        return $this->render('mission_listing/index.html.twig', [
            'missions' => $missions,
        ]);
    }
}
