<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['gallery:read', 'gallery:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gallery:read', 'gallery:write'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['gallery:read', 'gallery:write'])]
    private ?string $urlImage = null;

    #[ORM\ManyToOne(targetEntity: Habitat::class, inversedBy: 'galleries')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['gallery:read', 'gallery:write'])]
    private ?Habitat $habitat = null;

    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: 'galleries')]
    #[Groups(['gallery:read', 'gallery:write'])]
    private Collection $animals;

    #[ORM\OneToOne(mappedBy: 'gallery', cascade: ['persist', 'remove'])]
    private ?Service $service = null;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
    }

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

    public function getUrlImage(): ?string
    {
        return $this->urlImage;
    }

    public function setUrlImage(string $urlImage): self
    {
        $this->urlImage = $urlImage;
        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): self
    {
        $this->habitat = $habitat;
        return $this;
    }

    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function setAnimal(Animal $animal): self
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setGallery($this);
        }
        return $this;
    }

    public function removeAnimal(Animal $animal): self
    {
        if ($this->animals->removeElement($animal)) {
            $animal->removeGallery($this);
        }
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        // unset the owning side of the relation if necessary
        if ($service === null && $this->service !== null) {
            $this->service->setGallery(null);
        }

        // set the owning side of the relation if necessary
        if ($service !== null && $service->getGallery() !== $this) {
            $service->setGallery($this);
        }

        $this->service = $service;

        return $this;
    }
}
