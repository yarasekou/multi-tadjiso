<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'depenses')]
    private ?Station $station = null;

    /**
     * @var Collection<int, DetailDepense>
     */
    #[ORM\OneToMany(targetEntity: DetailDepense::class, mappedBy: 'depense')]
    private Collection $detailDepenses;

    public function __construct()
    {
        $this->detailDepenses = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

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
     * @return Collection<int, DetailDepense>
     */
    public function getDetailDepenses(): Collection
    {
        return $this->detailDepenses;
    }

    public function addDetailDepense(DetailDepense $detailDepense): static
    {
        if (!$this->detailDepenses->contains($detailDepense)) {
            $this->detailDepenses->add($detailDepense);
            $detailDepense->setDepense($this);
        }

        return $this;
    }

    public function removeDetailDepense(DetailDepense $detailDepense): static
    {
        if ($this->detailDepenses->removeElement($detailDepense)) {
            // set the owning side to null (unless already changed)
            if ($detailDepense->getDepense() === $this) {
                $detailDepense->setDepense(null);
            }
        }

        return $this;
    }
}
