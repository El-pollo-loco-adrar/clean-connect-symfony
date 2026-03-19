<?php

namespace App\Controller;

use App\Repository\EmployerRepository;
use Stripe\Webhook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeWebhookController extends AbstractController
{
    #[Route('/webhook/stripe', name: 'app_stripe_webhook', methods: ['POST'])]
    public function index(Request $request, EmployerRepository $employerRepository, EntityManagerInterface $em): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $endPointSecret = $this->getParameter('stripe_webhook_secret');

        try {
            // Vérification de la signature Stripe
            $event = Webhook::constructEvent($payload, $sigHeader, $endPointSecret);
        } catch(\UnexpectedValueException $e) {
            return new Response('payload invalide', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Signature invalide', 400);
        }

        // Cas n°1 : Le premier paiement est réussi(Abonnement initial)
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $stripeCustomerId = $session->customer;

            $employer = $employerRepository->findOneBy(['stripeCustomerId' => $stripeCustomerId]);

            if ($employer) {
                $employer->setSubscriptionStatus('active');
                // On récupère l'ID de l'abonnement créé
                $employer->setStripeSubscriptionId($session->subscription);
                $em->flush();
                return new Response('Statut mis à jour par l\'employeur', 200);
            }
        }

        //Cas n°2: un paiement de renouvellement a échoué
        if($event->type === 'invoice.payment_failed') {
            $invoice = $event->data->object;
            $employer = $employerRepository->findOneBy(['stripeCustomerId' => $invoice->customer]);

            if($employer) {
                // On passe le statut en "past_due" (en retard)
                // Le contrôleur AddMission bloquera l'accès car ce n'est plus "active"
                $employer->setSubscriptionStatus('past-due');
                $em->flush();
                return new Response('Abonnement suspendu pour impayé', 200);
            }
        }

        //Cas n°3 : l'abonnement est supprimé (via le portail Stripe)
        if($event->type === 'customer.subscription.deleted') {
            $subscription = $event->data->object;
            $employer = $employerRepository->findOneBy(['stripeCustomerId' => $subscription->customer]);

            if ($employer) {
                $employer->setSubscriptionStatus('canceled');
                $em->flush();
                return new Response('Abonnement résilié.', 200);
            }
        }

        return new Response('Webhook reçu et traité', 200);
    }
}
