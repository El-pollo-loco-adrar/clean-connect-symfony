<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Skills;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le titre est requis.")]
    #[Assert\Length(min: 3, minMessage: "Le titre doit faire au moins {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\séèêëàâäôöûüùç,.'#+-]+$/",
        message: "Le titre contient des caractères invalides."
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est requise.")]
    #[Assert\Length(min: 10, minMessage: "La description doit faire au moins {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\séèêëàâäôöûüùç,;:.'#+-]+$/",
        message: "La description contient des caractères invalides."
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual("today", message: "La date ne peut pas être dans le passé.")]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Assert\Expression(
        "this.getEndAt() > this.getStartAt()",
        message: "La date de fin doit être après la date de début."
    )]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if($this->createdAt === null){
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    #[ORM\ManyToOne(inversedBy: 'areaLocation')]
    private ?WageScale $wageScale = null;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?InterventionArea $areaLocation = null;

    #[ORM\ManyToOne(inversedBy: 'missions')]
    private ?Employer $employer = null;

    #[ORM\ManyToMany(targetEntity: Skills::class)]
    #[ORM\JoinTable(name:'mission_skills')]
    private Collection $skills;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getWageScale(): ?WageScale
    {
        return $this->wageScale;
    }

    public function setWageScale(?WageScale $wageScale): static
    {
        $this->wageScale = $wageScale;

        return $this;
    }

    public function getAreaLocation(): ?InterventionArea
    {
        return $this->areaLocation;
    }

    public function setAreaLocation(?InterventionArea $areaLocation): static
    {
        $this->areaLocation = $areaLocation;

        return $this;
    }

    public function getEmployer(): ?Employer
    {
        return $this->employer;
    }

    public function setEmployer(?Employer $employer): static
    {
        $this->employer = $employer;

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
}
