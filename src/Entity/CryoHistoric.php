<?php

namespace App\Entity;

use App\Repository\CryoHistoricRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoHistoricRepository::class)]
class CryoHistoric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["all"], inversedBy: 'cryoHistorics')]
    private ?CryoMedia $media = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(int $date): self
    {
        $this->date = $date;

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
}
