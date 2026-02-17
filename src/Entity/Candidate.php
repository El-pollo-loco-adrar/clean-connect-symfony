<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use App\Entity\Skills;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate extends User
{
    //Je supprime les propriétés redondantes héritées de User
    // #[ORM\Id]
    // #[ORM\GeneratedValue]
    // #[ORM\Column]
    // private ?int $id = null;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'candidate', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $availabilities;

    /**
     * @var Collection<int, Skills>
     */
    #[ORM\ManyToMany(targetEntity: Skills::class, inversedBy: 'candidates')]
    private Collection $skills;

    /**
     * @var Collection<int, InterventionArea>
     */
    #[ORM\ManyToMany(targetEntity: InterventionArea::class, inversedBy: 'candidates')]
    private Collection $interventionArea;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->interventionArea = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setCandidate($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getCandidate() === $this) {
                $availability->setCandidate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skills>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skills $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    /**
     * @return Collection<int, InterventionArea>
     */
    public function getInterventionArea(): Collection
    {
        return $this->interventionArea;
    }

    public function addInterventionArea(InterventionArea $interventionArea): static
    {
        if (!$this->interventionArea->contains($interventionArea)) {
            $this->interventionArea->add($interventionArea);
        }

        return $this;
    }

    public function removeInterventionArea(InterventionArea $interventionArea): static
    {
        $this->interventionArea->removeElement($interventionArea);

        return $this;
    }
}
