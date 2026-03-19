<?php
// le contrôleur qui gère la page "Mes Achats
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    #[Route('/employer/achats', name: 'app_employer_purchases')]
    public function index(): Response
    {
        $employer = $this->getUser();

        return $this->render('purchase/purchases.html.twig', [
            'employer' => $employer,
        ]);
    }
}
