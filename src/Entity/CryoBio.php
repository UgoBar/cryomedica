<?php

namespace App\Entity;

use App\Repository\CryoBioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoBioRepository::class)]
class CryoBio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade:["all"], inversedBy: 'cryoBios')]
    private ?CryoMedia $media = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $firstText = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $secondText = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedia(): ?CryoMedia
    {
        return $this->media;
    }

    public function setMedia(?CryoMedia $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getFirstText(): ?string
    {
        return $this->firstText;
    }

    public function setFirstText(?string $firstText): self
    {
        $this->firstText = $firstText;

        return $this;
    }

    public function getSecondText(): ?string
    {
        return $this->secondText;
    }

    public function setSecondText(?string $secondText): self
    {
        $this->secondText = $secondText;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
