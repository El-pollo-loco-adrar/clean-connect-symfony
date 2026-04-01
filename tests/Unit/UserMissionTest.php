<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Employer;
use App\Entity\Mission;
use PHPUnit\Framework\TestCase;

class UserMissionTest extends TestCase
{
    /**
     * Teste la logique de complétion du profil
     */
    public function testIsProfilComplete(): void
    {
        $user = new User();
        
        // 1. Au départ, le profil est vide
        $this->assertFalse($user->isProfilComplete());

        // 2. On remplit les champs de base
        $user->setFirstname('Jean')
            ->setLastname('Dupont')
            ->setPhoneNumber('0601020304');

        $this->assertTrue($user->isProfilComplete());
    }

    /**
     * Teste la logique spécifique à l'entité Employer (Héritage)
     */
    public function testEmployerProfilAndSubscription(): void
    {
        $employer = new Employer();
        $employer->setFirstname('Lucie')
                ->setLastname('Martin')
                ->setPhoneNumber('0700000000');

        // Un employeur sans nom d'entreprise n'a pas un profil complet
        $this->assertFalse($employer->isProfilComplete());

        $employer->setCompanyName('Nettoyage Express');
        $this->assertTrue($employer->isProfilComplete());

        // Test de la logique d'abonnement
        $this->assertFalse($employer->hasActiveSubscription());
        $employer->setSubscriptionStatus('active');
        $this->assertTrue($employer->hasActiveSubscription());
    }

    /**
     * Teste les méthodes de type (__toString)
     */
    public function testEntitiesToString(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', (string)$user);

        $user->setFirstname('Momo');
        $this->assertEquals('Momo', (string)$user);

        $mission = new Mission();
        $mission->setTitle('Nettoyage de bureau');
        $this->assertEquals('Nettoyage de bureau', (string)$mission);
    }

    /**
     * Teste la logique de date des missions
     */
    public function testMissionDateLogic(): void
    {
        $mission = new Mission();
        $now = new \DateTimeImmutable();
        $tomorrow = $now->modify('+1 day');

        $mission->setStartAt($now);
        $mission->setEndAt($tomorrow);

        $this->assertSame($now, $mission->getStartAt());
        $this->assertGreaterThan($mission->getStartAt(), $mission->getEndAt());
    }
}