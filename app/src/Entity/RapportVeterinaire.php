<?php

namespace App\Entity;

use App\Repository\RapportVeterinaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: RapportVeterinaireRepository::class)]
class RapportVeterinaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?\DateTimeImmutable $Date = null;


    #[ORM\Column(length: 50)]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?string $detail = null;

    #[ORM\ManyToOne(inversedBy: 'rapportVeterinaires', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    #[MaxDepth(1)]
    private ?Animal $animal = null;

    #[ORM\Column(length: 50)]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?string $etat_animal = null;

    #[ORM\Column(length: 50)]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?string $nourriture = null;

    #[ORM\Column]
    #[Groups(['rapportVeterinaire:read', 'rapportVeterinaire:write'])]
    private ?int $nourriture_grammage = null;

    // #[ORM\ManyToMany(targetEntity: AnimalFeeding::class, mappedBy: 'RapportVeterinaire')]
    // private Collection $animalFeedings;

    // public function __construct()
    // {
    //     $this->animalFeedings = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->Date;
    }

    public function setDate(\DateTimeImmutable $Date): static
    {
        $this->Date = $Date;
        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getEtatAnimal(): ?string
    {
        return $this->etat_animal;
    }

    public function setEtatAnimal(string $etat_animal): static
    {
        $this->etat_animal = $etat_animal;

        return $this;
    }

    public function getNourriture(): ?string
    {
        return $this->nourriture;
    }

    public function setNourriture(string $nourriture): static
    {
        $this->nourriture = $nourriture;

        return $this;
    }

    public function getNourritureGrammage(): ?int
    {
        return $this->nourriture_grammage;
    }

    public function setNourritureGrammage(int $nourriture_grammage): static
    {
        $this->nourriture_grammage = $nourriture_grammage;

        return $this;
    }

    // /**
    //  * @return Collection<int, AnimalFeeding>
    //  */
    // public function getAnimalFeedings(): Collection
    // {
    //     return $this->animalFeedings;
    // }

    // public function addAnimalFeeding(AnimalFeeding $animalFeeding): static
    // {
    //     if (!$this->animalFeedings->contains($animalFeeding)) {
    //         $this->animalFeedings->add($animalFeeding);
    //         $animalFeeding->addRapportVeterinaire($this);
    //     }

    //     return $this;
    // }

    // public function removeAnimalFeeding(AnimalFeeding $animalFeeding): static
    // {
    //     if ($this->animalFeedings->removeElement($animalFeeding)) {
    //         $animalFeeding->removeRapportVeterinaire($this);
    //     }

    //     return $this;
    // }
}
