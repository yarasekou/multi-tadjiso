<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    private ?Structure $structure = null;

    /**
     * @var Collection<int, Cuve>
     */
    #[ORM\OneToMany(targetEntity: Cuve::class, mappedBy: 'station')]
    private Collection $cuves;

    /**
     * @var Collection<int, TypeCarburant>
     */
    #[ORM\OneToMany(targetEntity: TypeCarburant::class, mappedBy: 'station')]
    private Collection $typeCarburants;

    /**
     * @var Collection<int, Pompe>
     */
    #[ORM\OneToMany(targetEntity: Pompe::class, mappedBy: 'station')]
    private Collection $pompes;

    /**
     * @var Collection<int, Depense>
     */
    #[ORM\OneToMany(targetEntity: Depense::class, mappedBy: 'station')]
    private Collection $depenses;

    /**
     * @var Collection<int, ClientStation>
     */
    #[ORM\OneToMany(targetEntity: ClientStation::class, mappedBy: 'station')]
    private Collection $clientStations;

    public function __construct()
    {
        $this->cuves = new ArrayCollection();
        $this->typeCarburants = new ArrayCollection();
        $this->pompes = new ArrayCollection();
        $this->depenses = new ArrayCollection();
        $this->clientStations = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): static
    {
        $this->structure = $structure;

        return $this;
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
            $cuve->setStation($this);
        }

        return $this;
    }

    public function removeCuve(Cuve $cuve): static
    {
        if ($this->cuves->removeElement($cuve)) {
            // set the owning side to null (unless already changed)
            if ($cuve->getStation() === $this) {
                $cuve->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeCarburant>
     */
    public function getTypeCarburants(): Collection
    {
        return $this->typeCarburants;
    }

    public function addTypeCarburant(TypeCarburant $typeCarburant): static
    {
        if (!$this->typeCarburants->contains($typeCarburant)) {
            $this->typeCarburants->add($typeCarburant);
            $typeCarburant->setStation($this);
        }

        return $this;
    }

    public function removeTypeCarburant(TypeCarburant $typeCarburant): static
    {
        if ($this->typeCarburants->removeElement($typeCarburant)) {
            // set the owning side to null (unless already changed)
            if ($typeCarburant->getStation() === $this) {
                $typeCarburant->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pompe>
     */
    public function getPompes(): Collection
    {
        return $this->pompes;
    }

    public function addPompe(Pompe $pompe): static
    {
        if (!$this->pompes->contains($pompe)) {
            $this->pompes->add($pompe);
            $pompe->setStation($this);
        }

        return $this;
    }

    public function removePompe(Pompe $pompe): static
    {
        if ($this->pompes->removeElement($pompe)) {
            // set the owning side to null (unless already changed)
            if ($pompe->getStation() === $this) {
                $pompe->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Depense>
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(Depense $depense): static
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses->add($depense);
            $depense->setStation($this);
        }

        return $this;
    }

    public function removeDepense(Depense $depense): static
    {
        if ($this->depenses->removeElement($depense)) {
            // set the owning side to null (unless already changed)
            if ($depense->getStation() === $this) {
                $depense->setStation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClientStation>
     */
    public function getClientStations(): Collection
    {
        return $this->clientStations;
    }

    public function addClientStation(ClientStation $clientStation): static
    {
        if (!$this->clientStations->contains($clientStation)) {
            $this->clientStations->add($clientStation);
            $clientStation->setStation($this);
        }

        return $this;
    }

    public function removeClientStation(ClientStation $clientStation): static
    {
        if ($this->clientStations->removeElement($clientStation)) {
            // set the owning side to null (unless already changed)
            if ($clientStation->getStation() === $this) {
                $clientStation->setStation(null);
            }
        }

        return $this;
    }
}
