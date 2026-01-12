<?php

namespace App\Controller\Admin;

use App\Repository\MissionRepository;
use App\Repository\CandidateRepository;
use App\Entity\Mission;
use App\Entity\SkillCategory;
use App\Entity\Skills;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private MissionRepository $missionRepository;
    private CandidateRepository $candidateRepository;

    public function __construct(
        MissionRepository $missionRepository,
        CandidateRepository $candidateRepository
    ) {
        $this->missionRepository = $missionRepository;
        $this->candidateRepository = $candidateRepository;
    }

    public function index(): Response
    {
        return $this->render('admin/my_dashboard.html.twig', [
        'count_missions' => $this->missionRepository->count([]),
        'count_candidates' => $this->candidateRepository->count([])
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Clean Connect');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('GEstion des missions');
        yield MenuItem::linkToCrud('Gérer les missions', 'fas fa-list', Mission::class);

        yield MenuItem::section('Paramètres Techniques');
        yield MenuItem::linkToCrud('Compétences', 'fas fa-wrench', Skills::class);
    }
}