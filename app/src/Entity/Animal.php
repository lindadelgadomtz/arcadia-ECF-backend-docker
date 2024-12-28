<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['animal:read', 'animal:write', 'habitat:read', 'rapportVeterinaire:read', 'animalFeeding:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['animal:read', 'animal:write', 'rapportVeterinaire:read', 'animalFeeding:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    #[Groups(['animal:read', 'animal:write', 'rapportVeterinaire:read', 'animalFeeding:read'])]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: RapportVeterinaire::class, cascade: ["persist"], orphanRemoval: true)]
    #[MaxDepth(1)]
    private Collection $rapportVeterinaires;

    #[ORM\ManyToOne(inversedBy: 'animals', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['animal:read', 'animal:write', 'rapportVeterinaire:read', 'animalFeeding:read'])]
    #[MaxDepth(1)]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'animals', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['animal:read', 'animal:write', 'rapportVeterinaire:read', 'animalFeeding:read'])]
    #[MaxDepth(1)]
    private ?Habitat $habitat = null;

    #[ORM\ManyToMany(targetEntity: Gallery::class, inversedBy: 'animals')]
    #[Groups(['animal:read', 'animal:write', 'gallery:read'])]
    #[MaxDepth(1)]
    private Collection $gallery;

    #[ORM\ManyToMany(targetEntity: AnimalFeeding::class, mappedBy: 'Animal')]
    #[Groups(['animal:read', 'animal:write', 'animalFeeding:write'])]
    #[MaxDepth(1)]
    private Collection $animalFeedings;

    public function __construct()
    {
        $this->rapportVeterinaires = new ArrayCollection();
        $this->gallery = new ArrayCollection();
        $this->animalFeedings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getRapportVeterinaires(): Collection
    {
        return $this->rapportVeterinaires;
    }

    public function addRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapportVeterinaires->contains($rapportVeterinaire)) {
            $this->rapportVeterinaires->add($rapportVeterinaire);
            $rapportVeterinaire->setAnimal($this);
        }
        return $this;
    }

    public function removeRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapportVeterinaires->removeElement($rapportVeterinaire)) {
            if ($rapportVeterinaire->getAnimal() === $this) {
                $rapportVeterinaire->setAnimal(null);
            }
        }
        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;
        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;
        return $this;
    }

    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    public function setGallery(Gallery $gallery): static
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery->add($gallery);
            $gallery->setAnimal($this); 
        }
        return $this;
    }

    public function removeGallery(Gallery $gallery): static
    {
        if ($this->gallery->removeElement($gallery)) {
            if ($gallery->getAnimals() === $this) {
                $gallery->setAnimal($this);
            }
        }
        return $this;
    }

    public function getAnimalFeedings(): Collection
    {
        return $this->animalFeedings;
    }

    public function addAnimalFeeding(AnimalFeeding $animalFeeding): static
    {
        if (!$this->animalFeedings->contains($animalFeeding)) {
            $this->animalFeedings->add($animalFeeding);
            $animalFeeding->addAnimal($this);
        }

        return $this;
    }

    public function removeAnimalFeeding(AnimalFeeding $animalFeeding): static
    {
        if ($this->animalFeedings->removeElement($animalFeeding)) {
            $animalFeeding->removeAnimal($this);
        }

        return $this;
    }
}