<?php

namespace App\Entity;

use App\Repository\CryoCustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoCustomerRepository::class)]
class CryoCustomer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["all"], inversedBy: 'cryoCustomers')]
    private ?CryoMedia $media = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
