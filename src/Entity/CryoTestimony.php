<?php

namespace App\Entity;

use App\Repository\CryoTestimonyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoTestimonyRepository::class)]
class CryoTestimony
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["all"], inversedBy: 'cryoTestimonies')]
    private ?CryoMedia $media = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $signature = null;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }
}
