<?php

namespace App\Entity;

use App\Repository\CryoCentersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryoCentersRepository::class)]
class CryoCenters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $zip = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?bool $is_open = null;

    #[ORM\OneToMany(mappedBy: 'center', targetEntity: CryoContact::class)]
    private Collection $cryoContacts;

    public function __construct()
    {
        $this->cryoContacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?int
    {
        return $this->zip;
    }

    public function setZip(int $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isIsOpen(): ?bool
    {
        return $this->is_open;
    }

    public function setIsOpen(bool $is_open): self
    {
        $this->is_open = $is_open;

        return $this;
    }

    /**
     * @return Collection<int, CryoContact>
     */
    public function getCryoContacts(): Collection
    {
        return $this->cryoContacts;
    }

    public function addCryoContact(CryoContact $cryoContact): self
    {
        if (!$this->cryoContacts->contains($cryoContact)) {
            $this->cryoContacts[] = $cryoContact;
            $cryoContact->setCenter($this);
        }

        return $this;
    }

    public function removeCryoContact(CryoContact $cryoContact): self
    {
        if ($this->cryoContacts->removeElement($cryoContact)) {
            // set the owning side to null (unless already changed)
            if ($cryoContact->getCenter() === $this) {
                $cryoContact->setCenter(null);
            }
        }

        return $this;
    }
}
