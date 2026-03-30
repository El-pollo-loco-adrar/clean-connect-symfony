<?php

namespace App\Service;

use App\Entity\Employer;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct(
        private string $stripeSecretKey,
        private EntityManagerInterface $entityManager
    ){
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    /**
     * Crée un client chez Stripe s'il n'en a pas encore
     */
    public function createStripeCustomer(Employer $employer): string
    {
        if ($employer->getStripeCustomerId()) {
            return $employer->getStripeCustomerId();
        }

        $customer = $this->stripe->customers->create([
            'email' => $employer->getEmail(),
            'name' => $employer->getCompanyName() ?? $employer->getFirstname() . ' ' . $employer->getLastname(),
            'metadata' => [
                'user_id' => $employer->getId()
            ]
        ]);

        $employer->setStripeCustomerId($customer->id);
        $this->entityManager->flush();

        return $customer->id;
    }

    /**
     * Génère un lien vers Checkout pour un abonnement simple
     */
    public function createCheckoutSession(Employer $employer, string $fixedPriceId, string $successUrl, string $cancelUrl): string
    {
        $customerId = $this->createStripeCustomer($employer);

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $fixedPriceId,
                    'quantity' => 1,// C'est l'ID du produit à 5€/mois créé sur Stripe
                ],
            ],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session->url;
    }

    /**
     * Permet au client de gérer son abonnement (annuler, changer de carte)
     */
    public function createCustomerPortalSession(Employer $employer, string $returnUrl): string
    {
        $session = $this->stripe->billingPortal->sessions->create([
            'customer' => $employer->getStripeCustomerId(),
            'return_url' => $returnUrl,
        ]);

        return $session->url;
    }
}