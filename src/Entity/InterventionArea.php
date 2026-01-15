<?php

namespace App\Entity;

use App\Repository\InterventionAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionAreaRepository::class)]
class InterventionArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $city = null;

    #[ORM\Column(length: 5, unique: true)]
    private ?string $postalCode = null;

    /**
     * @var Collection<int, Candidate>
     */
    #[ORM\ManyToMany(targetEntity: Candidate::class, mappedBy: 'interventionArea')]
    private Collection $candidates;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'areaLocation')]
    private Collection $missions;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
        $this->missions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): static
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates->add($candidate);
            $candidate->addInterventionArea($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            $candidate->removeInterventionArea($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->setAreaLocation($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getAreaLocation() === $this) {
                $mission->setAreaLocation(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    // On affiche la ville et le code postal
    return sprintf('%s (%s)', $this->city, $this->postalCode);
}
}
