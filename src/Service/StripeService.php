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
     * Génère un lien vers le tunnel de paiement (Checkout)
     */
    public function createCheckoutSession(Employer $employer, string $fixedPriceId, string $meteredPriceId, string $successUrl, string $cancelUrl): string
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
                [
                    'price' => $meteredPriceId,
                ]
            ],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return $session->url;
    }

    public function createCustomerPortalSession(Employer $employer, string $returnUrl): string
    {
        $session = $this->stripe->billingPortal->sessions->create([
            'customer' => $employer->getStripeCustomerId(),
            'return_url' => $returnUrl,
        ]);

        return $session->url;
    }

    public function incrementMissionUsage($subscriptionId): void
    {
        //! 1. Récupérer l'abonnement pour trouver l'ID de la ligne correspondante au prix "par mission"
        $subscription = $this->stripe->subscriptions->retrieve($subscriptionId);

        // On cherche l'item qui n'est pas le forfait fixe (ou on prend le deuxième item)
        // Ici, on part du principe que le prix à l'usage est le deuxième item de l'abonnement
        $usageItem = null;
        foreach ($subscription->items->data as $item) {
            dump($item->price->recurring->usage_type);

            if (isset($item->price->recurring) && $item->price->recurring->usage_type === 'metered') {
                $usageItem = $item;
                break;
            }
        }

        if ($usageItem) {
            //! 2. Créer un rapport d'utilisation
            $this->stripe->subscriptionItems->createUsageRecord(
                $usageItem->id,
                [
                    'quantity' => 1,
                    'timestamp' => time(),
                    'action' => 'increment',
                ]
            );
            dump("SUCCESS : Envoi à Stripe réussi !"); die();
            } else {
                //dump("ERREUR : Aucun item 'metered' trouvé dans l'abonnement " . $subscriptionId); die();
                }
    }
}