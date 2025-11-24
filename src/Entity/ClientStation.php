<?php

namespace App\Entity;

use App\Repository\ClientStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientStationRepository::class)]
class ClientStation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $fuelPrice = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'clientStations')]
    private ?Station $station = null;

    /**
     * @var Collection<int, BonClient>
     */
    #[ORM\OneToMany(targetEntity: BonClient::class, mappedBy: 'clientStation')]
    private Collection $bonClients;

    public function __construct()
    {
        $this->bonClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getFuelPrice(): ?array
    {
        return $this->fuelPrice;
    }

    public function setFuelPrice(?array $fuelPrice): static
    {
        $this->fuelPrice = $fuelPrice;

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

    /**
     * @return Collection<int, BonClient>
     */
    public function getBonClients(): Collection
    {
        return $this->bonClients;
    }

    public function addBonClient(BonClient $bonClient): static
    {
        if (!$this->bonClients->contains($bonClient)) {
            $this->bonClients->add($bonClient);
            $bonClient->setClientStation($this);
        }

        return $this;
    }

    public function removeBonClient(BonClient $bonClient): static
    {
        if ($this->bonClients->removeElement($bonClient)) {
            // set the owning side to null (unless already changed)
            if ($bonClient->getClientStation() === $this) {
                $bonClient->setClientStation(null);
            }
        }

        return $this;
    }
}
