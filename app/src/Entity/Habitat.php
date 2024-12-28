<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\HabitatRepository;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\MaxDepth;


#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['habitat:read', 'habitat:write', 'animal:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['habitat:read', 'habitat:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 80)]
    #[Groups(['habitat:read', 'habitat:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['habitat:read', 'habitat:write'])]
    private ?string $commentaire_habitat = null;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Animal::class, cascade: ["persist"])]
    #[Groups(['habitat:read'])]
    private Collection $animals;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Image::class, cascade: ["persist"])]
    #[Groups(['habitat:read'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'habitat', targetEntity: Gallery::class)]
    #[Groups(['habitat:read'])]
    private Collection $gallery;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->gallery = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCommentaireHabitat(): ?string
    {
        return $this->commentaire_habitat;
    }

    public function setCommentaireHabitat(?string $commentaire_habitat): static
    {
        $this->commentaire_habitat = $commentaire_habitat;
        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setHabitat($this);
        }
        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            if ($animal->getHabitat() === $this) {
                $animal->setHabitat(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setHabitat($this);
        }
        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getHabitat() === $this) {
                $image->setHabitat(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    public function addGallery(Gallery $gallery): static
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery->add($gallery);
            $gallery->setHabitat($this);
        }
        return $this;
    }

    public function removeGallery(Gallery $gallery): static
    {
        if ($this->gallery->removeElement($gallery)) {
            if ($gallery->getHabitat() === $this) {
                $gallery->setHabitat(null);
            }
        }
        return $this;
    }
}
