<?php

namespace App\Controller;

use App\Entity\Employer;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SubscriptionController extends AbstractController
{
    #[Route('/employer/subscription', name: 'app_subscription_subscribe')]
    public function subscribe(StripeService $stripeService): Response
    {
        $user = $this->getUser();

        // On vérifie que c'est bien un Employeur
        if (!$user instanceof Employer) {
            $this->addFlash('error', 'Seuls les recruteurs peuvent souscrire à un abonnement.');
            return $this->redirectToRoute('app_home');
        }

        // On récupère l'ID du prix fixe (5€) défini dans services.yaml
        $fixedPriceId = $this->getParameter('stripe_price_fixed');

        // On génère les URLs de retour absolues
        $successUrl = $this->generateUrl('app_subscription_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('app_subscription_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        //On crée la session de paiement à notre service
        $checkoutUrl = $stripeService->createCheckoutSession(
            $user,
            $fixedPriceId,
            $successUrl,
            $cancelUrl
            );

        return $this->redirect($checkoutUrl);
    }

    #[Route('/employer/subscription/success', name: 'app_subscription_success')]
    public function success(): Response
    {
        $this->addFlash('success', 'Féliciations ! Votre abonnement est désormais actif.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/employer/subscription/cancel', name: 'app_subscription_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('warning', 'Le paiement a été annulé. Vous avez besoin d\'un abonnement pour publier des missions.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/employer/portal', 'app_subscription_portal')]
    public function customerPortal(StripeService $stripeService): Response
    {
        /** @var Employer $user */
        $user = $this->getUser();

        // Sécurité : Si l'utilisateur n'a jamais été chez Stripe, on ne peut pas ouvrir de portail
        if(!$user || !$user->getStripeCustomerId()) {
            $this->addFlash('error', "Vous n'avez pas encore d'historique de paiement.");
            return $this->redirectToRoute('app_empoyer_purchases');
        }

        // On définit où l'utilisateur revient quand il quitte le portail Stripe
        $returnUrl = $this->generateUrl('app_employer_purchases', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // On génère l'URL du portail
        $portalUrl = $stripeService->createCustomerPortalSession($user, $returnUrl);

        return $this->redirect($portalUrl);

    }
}
