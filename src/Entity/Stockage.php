<?php

namespace App\Entity;

use App\Repository\StockageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockageRepository::class)]
class Stockage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $purchasePrice = null;

    #[ORM\Column]
    private ?int $missingQuantity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $isLast = null;

    #[ORM\ManyToOne(inversedBy: 'stockages')]
    private ?Cuve $cuve = null;

    #[ORM\ManyToOne(inversedBy: 'stockages')]
    private ?GlobalStockage $gloabalStockage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPurchasePrice(): ?int
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(int $purchasePrice): static
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    public function getMissingQuantity(): ?int
    {
        return $this->missingQuantity;
    }

    public function setMissingQuantity(int $missingQuantity): static
    {
        $this->missingQuantity = $missingQuantity;

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

    public function isLast(): ?bool
    {
        return $this->isLast;
    }

    public function setIsLast(bool $isLast): static
    {
        $this->isLast = $isLast;

        return $this;
    }

    public function getCuve(): ?Cuve
    {
        return $this->cuve;
    }

    public function setCuve(?Cuve $cuve): static
    {
        $this->cuve = $cuve;

        return $this;
    }

    public function getGloabalStockage(): ?GlobalStockage
    {
        return $this->gloabalStockage;
    }

    public function setGloabalStockage(?GlobalStockage $gloabalStockage): static
    {
        $this->gloabalStockage = $gloabalStockage;

        return $this;
    }
}
