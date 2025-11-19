<?php

namespace App\Entity;

use App\Repository\PistoletRepository;
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
}
