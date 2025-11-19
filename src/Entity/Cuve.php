<?php

namespace App\Entity;

use App\Repository\CuveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuveRepository::class)]
class Cuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'cuves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $station = null;

    #[ORM\ManyToOne(inversedBy: 'cuves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCarburant $typeCarburant = null;

    /**
     * @var Collection<int, Stockage>
     */
    #[ORM\OneToMany(targetEntity: Stockage::class, mappedBy: 'cuves')]
    private Collection $stockages;

    /**
     * @var Collection<int, Jaugeage>
     */
    #[ORM\OneToMany(targetEntity: Jaugeage::class, mappedBy: 'cuves')]
    private Collection $jaugeages;

    public function __construct()
    {
        $this->stockages = new ArrayCollection();
        $this->jaugeages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

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

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): static
    {
        $this->station = $station;

        return $this;
    }

    public function getTypeCarburant(): ?TypeCarburant
    {
        return $this->typeCarburant;
    }

    public function setTypeCarburant(?TypeCarburant $typeCarburant): static
    {
        $this->typeCarburant = $typeCarburant;

        return $this;
    }

    /**
     * @return Collection<int, Stockage>
     */
    public function getStockages(): Collection
    {
        return $this->stockages;
    }

    public function addStockage(Stockage $stockage): static
    {
        if (!$this->stockages->contains($stockage)) {
            $this->stockages->add($stockage);
            $stockage->setCuve($this);
        }

        return $this;
    }

    public function removeStockage(Stockage $stockage): static
    {
        if ($this->stockages->removeElement($stockage)) {
            // set the owning side to null (unless already changed)
            if ($stockage->getCuve() === $this) {
                $stockage->setCuve(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Jaugeage>
     */
    public function getJaugeages(): Collection
    {
        return $this->jaugeages;
    }

    public function addJaugeage(Jaugeage $jaugeage): static
    {
        if (!$this->jaugeages->contains($jaugeage)) {
            $this->jaugeages->add($jaugeage);
            $jaugeage->setCuve($this);
        }

        return $this;
    }

    public function removeJaugeage(Jaugeage $jaugeage): static
    {
        if ($this->jaugeages->removeElement($jaugeage)) {
            // set the owning side to null (unless already changed)
            if ($jaugeage->getCuve() === $this) {
                $jaugeage->setCuve(null);
            }
        }

        return $this;
    }

}
