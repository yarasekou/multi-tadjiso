<?php

namespace App\Entity;

use App\Repository\PistoletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PistoletRepository::class)]
class Pistolet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $indexPistolet = null;

    #[ORM\ManyToOne(targetEntity: Pompe::class, inversedBy: "pistolets")]
    private ?Pompe $pompe = null;

    #[ORM\ManyToOne(inversedBy: 'pistolets')]
    private ?TypeCarburant $typeCarburant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Indexation>
     */
    #[ORM\OneToMany(targetEntity: Indexation::class, mappedBy: 'pistolet')]
    private Collection $indexations;

    /**
     * @var Collection<int, VentePistolet>
     */
    #[ORM\OneToMany(targetEntity: VentePistolet::class, mappedBy: 'pistolet')]
    private Collection $ventePistolets;

    public function __construct()
    {
        $this->indexations = new ArrayCollection();
        $this->ventePistolets = new ArrayCollection();
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

    public function getIndexPistolet(): ?int
    {
        return $this->indexPistolet;
    }

    public function setIndexPistolet(int $indexPistolet): static
    {
        $this->indexPistolet = $indexPistolet;

        return $this;
    }

    public function getPompe(): ?Pompe
    {
        return $this->pompe;
    }

    public function setPompe(?Pompe $pompe): static
    {
        $this->pompe = $pompe;

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

    /**
     * @return Collection<int, Indexation>
     */
    public function getIndexations(): Collection
    {
        return $this->indexations;
    }

    public function addIndexation(Indexation $indexation): static
    {
        if (!$this->indexations->contains($indexation)) {
            $this->indexations->add($indexation);
            $indexation->setPistolet($this);
        }

        return $this;
    }

    public function removeIndexation(Indexation $indexation): static
    {
        if ($this->indexations->removeElement($indexation)) {
            // set the owning side to null (unless already changed)
            if ($indexation->getPistolet() === $this) {
                $indexation->setPistolet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VentePistolet>
     */
    public function getVentePistolets(): Collection
    {
        return $this->ventePistolets;
    }

    public function addVentePistolet(VentePistolet $ventePistolet): static
    {
        if (!$this->ventePistolets->contains($ventePistolet)) {
            $this->ventePistolets->add($ventePistolet);
            $ventePistolet->setPistolet($this);
        }

        return $this;
    }

    public function removeVentePistolet(VentePistolet $ventePistolet): static
    {
        if ($this->ventePistolets->removeElement($ventePistolet)) {
            // set the owning side to null (unless already changed)
            if ($ventePistolet->getPistolet() === $this) {
                $ventePistolet->setPistolet(null);
            }
        }

        return $this;
    }
}
