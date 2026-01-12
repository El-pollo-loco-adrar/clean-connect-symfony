<?php

namespace App\Entity;

use App\Repository\SkillsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillsRepository::class)]
class Skills
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nameSkill = null;

    #[ORM\ManyToOne(inversedBy: 'skills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SkillCategory $skillCategory = null;

    /**
     * @var Collection<int, Candidate>
     */
    #[ORM\ManyToMany(targetEntity: Candidate::class, mappedBy: 'skills')]
    private Collection $candidates;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameSkill(): ?string
    {
        return $this->nameSkill;
    }

    public function setNameSkill(string $nameSkill): static
    {
        $this->nameSkill = $nameSkill;

        return $this;
    }

    public function getSkillCategory(): ?SkillCategory
    {
        return $this->skillCategory;
    }

    public function setSkillCategory(?SkillCategory $skillCategory): static
    {
        $this->skillCategory = $skillCategory;

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
            $candidate->addSkill($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            $candidate->removeSkill($this);
        }

        return $this;
    }

        public function __toString(): string
    {
        if ($this->skillCategory) {
        return sprintf('%s (%s)', $this->nameSkill, $this->skillCategory->getNameCategory());
    }
    return $this->nameSkill ?? '';
    }
}
