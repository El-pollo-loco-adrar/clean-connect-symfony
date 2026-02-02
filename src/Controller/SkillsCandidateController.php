<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateSkillsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SkillsCandidateController extends AbstractController
{
    #[Route('/skills/candidate', name: 'app_skills_candidate')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $candidate = $this->getUser();

        if(!$candidate instanceof Candidate) {
            throw $this->createAccessDeniedException('Seuls les candidats peuvent accèder à cette page');
        }

        $form = $this->createForm(CandidateSkillsType::class, $candidate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vos compétences ont été mise à jour');
            return $this->redirectToRoute('app_skills_candidate');
        }


        return $this->render('skills_candidate/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
