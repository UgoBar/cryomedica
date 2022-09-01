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

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut être vide')]
    private ?string $alt = null;

    #[Vich\UploadableField(mapping: 'uploads', fileNameProperty: 'picture')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string')]
    private ?string $picture = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoBanner::class)]
    private Collection $title;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: CryoBanner::class)]
    private Collection $cryoBanners;

    public function __construct()
    {
        $this->title = new ArrayCollection();
        $this->cryoBanners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

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
}
