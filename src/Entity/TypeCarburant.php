<?php

namespace App\Entity;

use App\Repository\TypeCarburantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCarburantRepository::class)]
class TypeCarburant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $unitPrice = null;

    /**
     * @var Collection<int, Station>
     */
    #[ORM\ManyToMany(targetEntity: Station::class, inversedBy: 'typeCarburants')]
    private Collection $station;

    public function __construct()
    {
        $this->station = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStation(): Collection
    {
        return $this->station;
    }

    public function addStation(Station $station): static
    {
        if (!$this->station->contains($station)) {
            $this->station->add($station);
        }

        return $this;
    }

    public function removeStation(Station $station): static
    {
        $this->station->removeElement($station);

        return $this;
    }
}
