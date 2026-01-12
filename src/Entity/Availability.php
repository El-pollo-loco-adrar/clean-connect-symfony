<?php

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'occurs_on')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Day $day = null;

    #[ORM\ManyToOne(inversedBy: 'startAt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Time $startTime = null;

    #[ORM\ManyToOne(inversedBy: 'endAt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Time $endTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): static
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getDay(): ?Day
    {
        return $this->day;
    }

    public function setDay(?Day $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getStartTime(): ?Time
    {
        return $this->startTime;
    }

    public function setStartTime(?Time $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?Time
    {
        return $this->endTime;
    }

    public function setEndTime(?Time $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }
}
