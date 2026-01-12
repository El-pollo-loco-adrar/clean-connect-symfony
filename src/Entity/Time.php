<?php

namespace App\Entity;

use App\Repository\TimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeRepository::class)]
class Time
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, unique: true)]
    private ?string $hour = null;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'startTime')]
    private Collection $startAt;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'endTime')]
    private Collection $endAt;

    public function __construct()
    {
        $this->startAt = new ArrayCollection();
        $this->endAt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHour(): ?string
    {
        return $this->hour;
    }

    public function setHour(string $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getStartAt(): Collection
    {
        return $this->startAt;
    }

    public function addStartAt(Availability $startAt): static
    {
        if (!$this->startAt->contains($startAt)) {
            $this->startAt->add($startAt);
            $startAt->setStartTime($this);
        }

        return $this;
    }

    public function removeStartAt(Availability $startAt): static
    {
        if ($this->startAt->removeElement($startAt)) {
            // set the owning side to null (unless already changed)
            if ($startAt->getStartTime() === $this) {
                $startAt->setStartTime(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getEndAt(): Collection
    {
        return $this->endAt;
    }

    public function addEndAt(Availability $endAt): static
    {
        if (!$this->endAt->contains($endAt)) {
            $this->endAt->add($endAt);
            $endAt->setEndTime($this);
        }

        return $this;
    }

    public function removeEndAt(Availability $endAt): static
    {
        if ($this->endAt->removeElement($endAt)) {
            // set the owning side to null (unless already changed)
            if ($endAt->getEndTime() === $this) {
                $endAt->setEndTime(null);
            }
        }

        return $this;
    }
}
