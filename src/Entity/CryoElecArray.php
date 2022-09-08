<?php

namespace App\Entity;

use App\Repository\CryoElecArrayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoElecArrayRepository::class)]
class CryoElecArray
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'array')]
    private ?CryoElec $cryoElec = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCryoElec(): ?CryoElec
    {
        return $this->cryoElec;
    }

    public function setCryoElec(?CryoElec $cryoElec): self
    {
        $this->cryoElec = $cryoElec;

        return $this;
    }
}
