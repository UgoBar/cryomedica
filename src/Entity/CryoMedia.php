<?php

namespace App\Entity;

use App\Repository\CryoMediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CryoMediaRepository::class)]
#[Vich\Uploadable]
class CryoMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'uploads', fileNameProperty: 'picture')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoBanner::class)]
    private Collection $title;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoBanner::class)]
    private Collection $cryoBanners;

    public function __construct()
    {
        $this->title = new ArrayCollection();
        $this->cryoBanners = new ArrayCollection();
        $this->cryoPictos = new ArrayCollection();
        $this->cryoBios = new ArrayCollection();
        $this->cryoCustomers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoPicto::class)]
    private Collection $cryoPictos;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoBio::class)]
    private Collection $cryoBios;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alt = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoCustomer::class)]
    private Collection $cryoCustomers;

    /**
     * @param \Symfony\Component\HttpFoundation\File\File|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, CryoBanner>
     */
    public function getTitle(): Collection
    {
        return $this->title;
    }

    public function addTitle(CryoBanner $title): self
    {
        if (!$this->title->contains($title)) {
            $this->title[] = $title;
            $title->setMedia($this);
        }

        return $this;
    }

    public function removeTitle(CryoBanner $title): self
    {
        if ($this->title->removeElement($title)) {
            // set the owning side to null (unless already changed)
            if ($title->getMedia() === $this) {
                $title->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CryoBanner>
     */
    public function getCryoBanners(): Collection
    {
        return $this->cryoBanners;
    }

    public function addCryoBanner(CryoBanner $cryoBanner): self
    {
        if (!$this->cryoBanners->contains($cryoBanner)) {
            $this->cryoBanners[] = $cryoBanner;
            $cryoBanner->setMedia($this);
        }

        return $this;
    }

    public function removeCryoBanner(CryoBanner $cryoBanner): self
    {
        if ($this->cryoBanners->removeElement($cryoBanner)) {
            // set the owning side to null (unless already changed)
            if ($cryoBanner->getMedia() === $this) {
                $cryoBanner->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CryoPicto>
     */
    public function getCryoPictos(): Collection
    {
        return $this->cryoPictos;
    }

    public function addCryoPicto(CryoPicto $cryoPicto): self
    {
        if (!$this->cryoPictos->contains($cryoPicto)) {
            $this->cryoPictos[] = $cryoPicto;
            $cryoPicto->setMedia($this);
        }

        return $this;
    }

    public function removeCryoPicto(CryoPicto $cryoPicto): self
    {
        if ($this->cryoPictos->removeElement($cryoPicto)) {
            // set the owning side to null (unless already changed)
            if ($cryoPicto->getMedia() === $this) {
                $cryoPicto->setMedia(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CryoBio>
     */
    public function getCryoBios(): Collection
    {
        return $this->cryoBios;
    }

    public function addCryoBio(CryoBio $cryoBio): self
    {
        if (!$this->cryoBios->contains($cryoBio)) {
            $this->cryoBios[] = $cryoBio;
            $cryoBio->setMedia($this);
        }

        return $this;
    }

    public function removeCryoBio(CryoBio $cryoBio): self
    {
        if ($this->cryoBios->removeElement($cryoBio)) {
            // set the owning side to null (unless already changed)
            if ($cryoBio->getMedia() === $this) {
                $cryoBio->setMedia(null);
            }
        }

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return Collection<int, CryoCustomer>
     */
    public function getCryoCustomers(): Collection
    {
        return $this->cryoCustomers;
    }

    public function addCryoCustomer(CryoCustomer $cryoCustomer): self
    {
        if (!$this->cryoCustomers->contains($cryoCustomer)) {
            $this->cryoCustomers[] = $cryoCustomer;
            $cryoCustomer->setMedia($this);
        }

        return $this;
    }

    public function removeCryoCustomer(CryoCustomer $cryoCustomer): self
    {
        if ($this->cryoCustomers->removeElement($cryoCustomer)) {
            // set the owning side to null (unless already changed)
            if ($cryoCustomer->getMedia() === $this) {
                $cryoCustomer->setMedia(null);
            }
        }

        return $this;
    }
}
