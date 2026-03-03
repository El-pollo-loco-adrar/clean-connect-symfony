<?php

namespace App\Controller\Admin;

use App\Entity\Conversation;
use App\Entity\Day;
use App\Entity\InterventionArea;
use App\Entity\Message;
use App\Repository\MissionRepository;
use App\Repository\CandidateRepository;
use App\Repository\EmployerRepository;
use App\Entity\Mission;
use App\Entity\SkillCategory;
use App\Entity\Skills;
use App\Entity\Time;
use App\Entity\User;
use App\Entity\WageScale;
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
    private EmployerRepository $employerRepository;

    public function __construct(
        MissionRepository $missionRepository,
        CandidateRepository $candidateRepository,
        EmployerRepository $employerRepository
    ) {
        $this->missionRepository = $missionRepository;
        $this->candidateRepository = $candidateRepository;
        $this->employerRepository = $employerRepository;
    }

    public function index(): Response
    {
        return $this->render('admin/my_dashboard.html.twig', [
        'count_missions' => $this->missionRepository->count([]),
        'count_candidates' => $this->candidateRepository->count([]),
        'count_employers' => $this->employerRepository->count([]),
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

        
        yield MenuItem::section('Gestion des missions');
        yield MenuItem::linkToCrud('Gérer les missions', 'fas fa-list', Mission::class);

        //--------------------------------

        yield MenuItem::section('Utilisateurs');
        //Liste total
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        //Liste des candidats
        yield MenuItem::linkToCrud('Candidats', 'fas fa-user-ninja', User::class)
        ->setController(CandidateCrudController::class);

        //Liste des employeurs
        yield MenuItem::linkToCrud('Employeurs', 'fas fa-user-secret', User::class)
        ->setController(EmployerCrudController::class);

        //--------------------------------

        yield MenuItem::section('Paramètres Techniques');
        yield MenuItem::linkToCrud('Catégories', 'fa-solid fa-layer-group', SkillCategory::class);

        yield MenuItem::linkToCrud('Compétences', 'fas fa-wrench', Skills::class);

        //--------------------------------

        yield MenuItem::section('Horaires');
        yield MenuItem::linkToCrud('Jours de la semaine',
        "fa-solid fa-calendar-days", Day::class);

        yield MenuItem::linkToCrud('Heures', "fa-solid fa-clock", Time::class);

        //--------------------------------

        yield MenuItem::section('Salaire');
        yield MenuItem::linkToCrud('Taux horaires',
        "fa-solid fa-sack-dollar", WageScale::class);

        //--------------------------------

        yield MenuItem::section('Secteur');
        yield MenuItem::linkToCrud("Zones d'interventions", "fa-solid fa-location-arrow", InterventionArea::class);

        //--------------------------------

        yield MenuItem::section('Chat');
        yield MenuItem::linkToCrud("Conversations", "fa-solid fa-comment", Conversation::class);
    }
}