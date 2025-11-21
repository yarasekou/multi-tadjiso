<?php

namespace App\Entity;

use App\Repository\CuveMesureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CuveMesureRepository::class)]
class CuveMesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $levelCm = null;

    #[ORM\Column]
    private ?int $volume = null;

    #[ORM\ManyToOne(inversedBy: 'cuveMesures')]
    private ?Cuve $cuve = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevelCm(): ?int
    {
        return $this->levelCm;
    }

    public function setLevelCm(int $levelCm): static
    {
        $this->levelCm = $levelCm;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): static
    {
        $this->volume = $volume;

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
