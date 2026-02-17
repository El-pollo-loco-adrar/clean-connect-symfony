<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateAvailabilitiesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvailabilityController extends AbstractController
{
    #[Route('/availability', name: 'app_availability')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $candidate = $this->getUser();

        if(!$candidate instanceof Candidate) {
            throw $this->createAccessDeniedException('Accès réservé aux candidats.');
        }

        $form = $this->createForm(CandidateAvailabilitiesType::class, $candidate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Disponibilités mises à jour !');
            return $this->redirectToRoute('app_availability');
        }

        return $this->render('availability/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
