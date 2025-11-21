<?php

namespace App\Entity;

use App\Repository\IndexationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexationRepository::class)]
class Indexation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $valIndex = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?float $difference = null;

    #[ORM\ManyToOne(inversedBy: 'indexations')]
    private ?Pistolet $pistolet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValIndex(): ?float
    {
        return $this->valIndex;
    }

    public function setValIndex(float $valIndex): static
    {
        $this->valIndex = $valIndex;

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

    public function getDifference(): ?float
    {
        return $this->difference;
    }

    public function setDifference(float $difference): static
    {
        $this->difference = $difference;

        return $this;
    }

    public function getPistolet(): ?Pistolet
    {
        return $this->pistolet;
    }

    public function setPistolet(?Pistolet $pistolet): static
    {
        $this->pistolet = $pistolet;

        return $this;
    }
}
