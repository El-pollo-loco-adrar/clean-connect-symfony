<?php

namespace App\Entity;

use App\Repository\WageScaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use function PHPUnit\Framework\returnArgument;

#[ORM\Table(
    uniqueConstraints: [
        new ORM\UniqueConstraint(name:'niveau_level_unique', columns: ['niveau', 'level'])
    ]
)]

#[ORM\Entity(repositoryClass: WageScaleRepository::class)]
class WageScale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $niveau = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $hourlyRate = null;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'wageScale')]
    private Collection $mission;

    public function __construct()
    {
        $this->mission = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getHourlyRate(): ?string
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(string $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getAreaLocation(): Collection
    {
        return $this->mission;
    }

    public function addAreaLocation(Mission $mission): static
    {
        if (!$this->mission->contains($mission)) {
            $this->mission->add($mission);
            $mission->setWageScale($this);
        }

        return $this;
    }

    public function removeAreaLocation(Mission $mission): static
    {
        if ($this->mission->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getWageScale() === $this) {
                $mission->setWageScale(null);
            }
        }

        return $this;
    }
}
