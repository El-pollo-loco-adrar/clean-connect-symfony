<?php

namespace App\Controller;

use App\Entity\Employer;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowMissionController extends AbstractController
{
    #[Route('/show/mission', name: 'app_show_mission')]
    public function index(MissionRepository $missionRepository): Response
    {
        // 1.Seul un employeur peut voir cette page
        $user = $this->getUser();
        if (!$user instanceof Employer) {
            throw $this->createAccessDeniedException('Accès réservé aux employeurs.');
        }

        // 2. Récupère uniquement les missions liés à cet employeur
        $missions = $missionRepository->findBy(
            ['employer' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('show_mission/index.html.twig', [
            'missions' => $missions,
        ]);
    }
}
