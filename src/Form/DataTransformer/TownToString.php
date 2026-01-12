<?php

namespace App\Form\DataTransformer;

use App\Entity\InterventionArea;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TownToString implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    // De l'objet vers le texte (affichage)
    public function transform($area): string {
        if (null === $area) return '';
        return $area->getPostalCode() . ' - ' . $area->getCity();
    }

    // du texte vers l'objet (soumission)
    public function reverseTransform($value): ?InterventionArea {
        if (!$value) return null;

        // On sépare "31000 - Toulouse"
        $parts = explode(' - ', $value);
        $cp = trim($parts[0]);
        $city = isset($parts[1]) ? trim($parts[1]) : '';

        // 1. On cherche si ça existe déjà
        $area = $this->entityManager->getRepository(InterventionArea::class)->findOneBy([
            'postal_code' => $cp,
            'city' => $city
        ]);

        if ($area) return $area;

        // 2. Si ça n'existe pas, on crée le nouveau lieu
        $newArea = new InterventionArea();
        $newArea->setPostalCode($cp);
        $newArea->setCity($city ?: 'Ville inconnue');
        
        $this->entityManager->persist($newArea);
        
        return $newArea;
    }
}