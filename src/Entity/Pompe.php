<?php

namespace App\Entity;

use App\Repository\PompeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PompeRepository::class)]
class Pompe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'pompes')]
    private ?Station $station = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Pistolet>
     */
    #[ORM\OneToMany(
        targetEntity: Pistolet::class,
        mappedBy: "pompe",
        cascade: ["persist"],
        orphanRemoval: true
    )]
    private Collection $pistolets;

    public function __construct()
    {
        $this->pistolets = new ArrayCollection();
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

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): static
    {
        $this->station = $station;

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
            $pistolet->setPompe($this);
        }

        return $this;
    }

    public function removePistolet(Pistolet $pistolet): static
    {
        if ($this->pistolets->removeElement($pistolet)) {
            // set the owning side to null (unless already changed)
            if ($pistolet->getPompe() === $this) {
                $pistolet->setPompe(null);
            }
        }

        return $this;
    }
}
