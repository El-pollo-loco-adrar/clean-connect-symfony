<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Entity\Skills;
use App\Form\AddMissionType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Proxies\__CG__\App\Entity\SkillCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function PHPUnit\Framework\returnArgument;

final class AddMissionController extends AbstractController
{
    public function __construct( private EntityManagerInterface $em)
    {

    }

    #[Route('/create/mission', name: 'app_mission_create', methods:['GET','POST'])]
    public function createMission(Request $request): Response
    {

        //Création de l'objet mission
        $mission = new Mission();

        //Création du formulaire
        $form = $this->createForm(AddMissionType::class, $mission);

        //Récupérer les données du formulaire
        $form->handleRequest($request);

        //Récupération des skills
        $skills = $this->em->getRepository(Skills::class)->findAll();
        $skillsArray = array_map(fn($s) => [
            'id'=> $s->getId(),
            'label' => $s->getNameSkill()
        ], $skills);

        //Récupération des skill_category
        $categories = $this->em->getRepository(SkillCategory::class)->findAll();


        //Vérifier qu'on reçoit un formulaire + vérifier qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($mission);
            $this->em->flush();

            //Redirection
            return $this->redirectToRoute(
                'app_mission_create',
                ['message' => 'Mission créée avec succès ✅']
            );
        }

        
        
        $message = '';
        if(isset($_GET['message']) && !empty($_GET['message'])){
            $message = $_GET['message'];
        }

        //Affichage du formulaire en le passant à TWIG
        return $this->render('mission/create.html.twig', [
            'controller_name' => 'AddMissionController',
            'form' => $form->createView(),
            'message' => $message,
            'skills' => $skillsArray,
            'categories' => $categories,
        ]);
    }
}
