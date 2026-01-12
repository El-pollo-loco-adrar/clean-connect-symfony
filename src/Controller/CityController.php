<?php

namespace App\Controller;

use App\Repository\InterventionAreaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


final class CityController extends AbstractController
{
    #[Route('/api/cities', name: 'api_cities', methods: ['GET'])]
    public function search(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $query = $request->query->get('q', '');
        if (strlen($query) < 2) return new JsonResponse([]);

        // On interroge l'API officielle
        $response = $httpClient->request('GET', 'https://api-adresse.data.gouv.fr/search/', [
            'query' => [
                'q' => $query,
                'type' => 'municipality',
                'limit' => 10
            ]
        ]);

        $results = $response->toArray();
        $data = [];

        foreach ($results['features'] as $feature) {
            $props = $feature['properties'];
            // On renvoie le format "CodePostal - Ville"
            $data[] = [
                'text' => $props['postcode'] . ' - ' . $props['city']
            ];
        }

        return new JsonResponse($data);
    }
}
