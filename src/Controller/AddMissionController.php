<?php

namespace App\Controller;

use App\Entity\Employer;
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
        //Sécurité de la route
        if (!$this->getUser() instanceof Employer) {
            throw $this->createAccessDeniedException('Interdit si vous n\'êtes pas employeur.');
        }
        
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

            $mission = $form->getData();

            // Sécurité anti-doublon : on récupère l'area envoyée par le formulaire
            $area = $mission->getAreaLocation();

            if ($area) {
                // On vérifie si cette ville + CP existe déjà vraiment en BDD
                $existingArea = $this->em->getRepository(\App\Entity\InterventionArea::class)->findOneBy([
                    'city' => $area->getCity(),
                    'postalCode' => $area->getPostalCode()
                ]);

                if ($existingArea) {
                    // Si elle existe, on remplace l'objet actuel par celui de la BDD
                    // Cela empêche Doctrine de vouloir faire un "INSERT"
                    $mission->setAreaLocation($existingArea);
                } else {
                    // Si elle n'existe vraiment pas, on dit à Doctrine de la créer
                    $this->em->persist($area);
                }
            }

            //Je récupère l'ID de l'auteur de la mission
            $mission->setEmployer($this->getUser());

            $this->em->persist($mission);
            $this->em->flush();

            //Redirection
            $this->addFlash('success', 'Mission créée avec succès ✅');
            return $this->redirectToRoute('app_show_mission');
        } else {
            dump($form->getErrors(true, true));
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

    #[Route('/mission/delete/{id}', name: 'app_mission_delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $em): Response
    {
        // Vérification de sécurité CSRF
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $em->remove($mission);
            $em->flush();
            $this->addFlash('success', 'Mission supprimée avec succès.');
        }

        return $this->redirectToRoute('app_show_mission');
    }
}
