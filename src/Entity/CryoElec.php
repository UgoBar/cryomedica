<?php

namespace App\Entity;

use App\Repository\CryoElecRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoElecRepository::class)]
class CryoElec
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\OneToMany(mappedBy: 'cryoElec', targetEntity: CryoElecArray::class)]
    private Collection $array;

    public function __construct()
    {
        $this->array = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, CryoElecArray>
     */
    public function getArray(): Collection
    {
        return $this->array;
    }

    public function addArray(CryoElecArray $array): self
    {
        if (!$this->array->contains($array)) {
            $this->array[] = $array;
            $array->setCryoElec($this);
        }

        return $this;
    }

    public function removeArray(CryoElecArray $array): self
    {
        if ($this->array->removeElement($array)) {
            // set the owning side to null (unless already changed)
            if ($array->getCryoElec() === $this) {
                $array->setCryoElec(null);
            }
        }

        return $this;
    }
}
