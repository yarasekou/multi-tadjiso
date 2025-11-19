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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Cuve>
     */
    #[ORM\OneToMany(targetEntity: Cuve::class, mappedBy: 'typeCarburant')]
    private Collection $cuves;

    #[ORM\ManyToOne(inversedBy: 'typeCarburants')]
    private ?Station $station = null;

    /**
     * @var Collection<int, GlobalStockage>
     */
    #[ORM\OneToMany(targetEntity: GlobalStockage::class, mappedBy: 'typeCarburant')]
    private Collection $globalStockages;

    /**
     * @var Collection<int, Pistolet>
     */
    #[ORM\OneToMany(targetEntity: Pistolet::class, mappedBy: 'typeCarburant')]
    private Collection $pistolets;

    public function __construct()
    {
        $this->cuves = new ArrayCollection();
        $this->globalStockages = new ArrayCollection();
        $this->pistolets = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    /**
     * @return Collection<int, Cuve>
     */
    public function getCuves(): Collection
    {
        return $this->cuves;
    }

    public function addCuve(Cuve $cuve): static
    {
        if (!$this->cuves->contains($cuve)) {
            $this->cuves->add($cuve);
            $cuve->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeCuve(Cuve $cuve): static
    {
        if ($this->cuves->removeElement($cuve)) {
            // set the owning side to null (unless already changed)
            if ($cuve->getTypeCarburant() === $this) {
                $cuve->setTypeCarburant(null);
            }
        }

        return $this;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): static
    {
        $this->station = $station;

        return $this;
    }

    /**
     * @return Collection<int, GlobalStockage>
     */
    public function getGlobalStockages(): Collection
    {
        return $this->globalStockages;
    }

    public function addGlobalStockage(GlobalStockage $globalStockage): static
    {
        if (!$this->globalStockages->contains($globalStockage)) {
            $this->globalStockages->add($globalStockage);
            $globalStockage->setTypeCarburant($this);
        }

        return $this;
    }

    public function removeGlobalStockage(GlobalStockage $globalStockage): static
    {
        if ($this->globalStockages->removeElement($globalStockage)) {
            // set the owning side to null (unless already changed)
            if ($globalStockage->getTypeCarburant() === $this) {
                $globalStockage->setTypeCarburant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pistolet>
     */
    public function getPistolets(): Collection
    {
        return $this->pistolets;
    }

    public function addPistolet(Pistolet $pistolet): static
    {
        if (!$this->pistolets->contains($pistolet)) {
            $this->pistolets->add($pistolet);
            $pistolet->setTypeCarburant($this);
        }

        return $this;
    }

    public function removePistolet(Pistolet $pistolet): static
    {
        if ($this->pistolets->removeElement($pistolet)) {
            // set the owning side to null (unless already changed)
            if ($pistolet->getTypeCarburant() === $this) {
                $pistolet->setTypeCarburant(null);
            }
        }

        return $this;
    }
}
