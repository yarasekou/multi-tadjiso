<?php

namespace App\Entity;

use App\Repository\VenteCuveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VenteCuveRepository::class)]
class VenteCuve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $purchaseAmount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $saleAmount = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $profit = null;

    #[ORM\ManyToOne(inversedBy: 'venteCuves')]
    private ?Cuve $cuve = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPurchaseAmount(): ?string
    {
        return $this->purchaseAmount;
    }

    public function setPurchaseAmount(string $purchaseAmount): static
    {
        $this->purchaseAmount = $purchaseAmount;

        return $this;
    }

    public function getSaleAmount(): ?string
    {
        return $this->saleAmount;
    }

    public function setSaleAmount(string $saleAmount): static
    {
        $this->saleAmount = $saleAmount;

        return $this;
    }

    public function getProfit(): ?string
    {
        return $this->profit;
    }

    public function setProfit(string $profit): static
    {
        $this->profit = $profit;

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
}
