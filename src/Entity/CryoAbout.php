<?php

namespace App\Entity;

use App\Repository\CryoAboutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoAboutRepository::class)]
class CryoAbout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $firstText = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $secondText = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
