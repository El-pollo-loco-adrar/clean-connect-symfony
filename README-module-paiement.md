## 🚀 Architecture du Système

### 1. Modèle de Données
L'entité Employer a été enrichie pour suivre l'état des paiements :

    stripeCustomerId : Identifiant unique du client chez Stripe.
    
    stripeSubscriptionId : Identifiant de l'abonnement en cours.

    subscriptionStatus : État de l'abonnement (active, past_due, canceled).

### 2. Flux de Paiement (Checkout)
Route : /employer/subscription

Fonctionnement : Redirige l'employeur vers une page sécurisée Stripe. Une fois le paiement validé, l'utilisateur est redirigé vers le site

### 3. Automatisation via Webhook

Pour garantir la fiabilité, le site écoute les notifications de Stripe :

    Endpoint : /webhook/stripe (méthode POST)

    Événement écouté : checkout.session.completed

    Action : Dès réception, le statut de l'employeur passe en active en base de données, même s'il ferme son navigateur.


## 🛠 Configuration Locale

### 1. Variables d'environnement (.env.local) :
```bash
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_FIXED=price_...
STRIPE_PRICE_MISSION=price_...
```

### 2. Lancer le tunnel Webhook dans un terminal:
```bash
stripe listen --forward-to localhost:8000/webhook/stripe
```

### 3. Simuler un paiement réussi (Test) dans un autre terminal :
```bash
stripe trigger checkout.session.completed
```
* ne pas oublier d'ouvrir le serveur symfony serve

## 📝 Services et Contrôleurs

StripeService : Gère la création de clients et les sessions de paiement.

SubscriptionController : Gère les redirections vers Stripe et les pages de retour (Success/Cancel).

StripeWebhookController : Réceptionne et valide les signatures de Stripe pour mettre à jour la BDD.