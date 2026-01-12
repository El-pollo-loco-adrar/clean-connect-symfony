<?php

namespace App\Entity;

use App\Repository\DayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayRepository::class)]
class Day
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique:true)]
    private ?string $day = null;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'day')]
    private Collection $occurs_on;

    public function __construct()
    {
        $this->occurs_on = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getOccursOn(): Collection
    {
        return $this->occurs_on;
    }

    public function addOccursOn(Availability $occursOn): static
    {
        if (!$this->occurs_on->contains($occursOn)) {
            $this->occurs_on->add($occursOn);
            $occursOn->setDay($this);
        }

        return $this;
    }

    public function removeOccursOn(Availability $occursOn): static
    {
        if ($this->occurs_on->removeElement($occursOn)) {
            // set the owning side to null (unless already changed)
            if ($occursOn->getDay() === $this) {
                $occursOn->setDay(null);
            }
        }

        return $this;
    }
}
