<?php

namespace App\Controller;

use App\Form\CandidateInterventionAreaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InterventionAreaController extends AbstractController
{
    #[Route('/profile/area', name: 'app_candidate_areas')]
    public function manageAreas(Request $request, EntityManagerInterface $em): Response
    {
        $candidate = $this->getUser();
        if(!$candidate instanceof \App\Entity\Candidate) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CandidateInterventionAreaType::class, $candidate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $areas = $candidate->getInterventionArea();

            foreach($areas as $area) {
                $existingArea = $em->getRepository(\App\Entity\InterventionArea::class)->findOneBy([
                    'city' => $area->getCity(),
                    'postalCode' => $area->getPostalCode()
                ]);

                if($existingArea) {
                    // Si elle existe, on remplace l'objet par celui de la BDD
                    $candidate->removeInterventionArea($area);
                    $candidate->addInterventionArea($existingArea);
                } else {
                    $em->persist($area);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Vos zones de déplacements sont enregistrées !');
            return $this->redirectToRoute('app_candidate_areas');
        }

        return $this->render('intervention_area/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
