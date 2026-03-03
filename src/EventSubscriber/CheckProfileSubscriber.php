<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class CheckProfileSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private RouterInterface $router
    )
    {}

    public function onKernelRequest(RequestEvent $event)
    {
        // On ne fait rien si ce n'est pas la requête principale
        if (!$event->isMainRequest()){
            return;
        }

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        // On récupère l'utilisateur connecté
        $user = $this->security->getUser();

        // LOGIQUE :
        // Si l'utilisateur est connecté
        // ET que son profil n'est pas complet
        // ET qu'il n'est pas déjà sur la page de modification (pour éviter la boucle infinie)
        // ET qu'il ne s'agit pas d'une route de déconnexion ou de ressources (js/css)
        if ($user instanceof User && !$user->isProfilComplete()){

            // Liste des routes autorisées (on doit le laisser accéder au formulaire et à la déconnexion)
            $allowedRoutes = ['app_profile_complete', 'app_logout', '_wdt', '_profiler'];

            if (!in_array($routeName, $allowedRoutes)){
                $event->setResponse(new RedirectResponse($this->router->generate('app_profile_complete')));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}