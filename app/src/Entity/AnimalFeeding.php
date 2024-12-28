<?php

namespace App\Entity;

use App\Repository\AnimalFeedingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: AnimalFeedingRepository::class)]
class AnimalFeeding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['animalFeeding:read', 'animalFeeding:write', 'animal:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['animalFeeding:read', 'animalFeeding:write', 'animal:read'])]
    private ?\DateTimeImmutable $Date = null;

    #[ORM\Column(length: 255)]
    #[Groups(['animalFeeding:read', 'animalFeeding:write', 'animal:read'])]
    private ?string $Nourriture = null;

    #[ORM\Column]
    #[Groups(['animalFeeding:read', 'animalFeeding:write', 'animal:read'])]
    private ?int $Nourriture_grammage_emp = null;

    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: 'animalFeedings')]
    #[Groups(['animalFeeding:read'])]
    #[MaxDepth(1)]
    private Collection $Animal;

    // #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'animalFeedings')]
    // #[Groups(['animalFeeding:read', 'user:read'])]
    // #[MaxDepth(1)]
    // private Collection $User;

    // #[ORM\ManyToMany(targetEntity: RapportVeterinaire::class, inversedBy: 'animalFeedings')]
    // #[Groups(['animalFeeding:read', 'rapportVeterinaire:read'])]
    // #[MaxDepth(1)]
    // private Collection $RapportVeterinaire;

    public function __construct()
    {
        $this->Animal = new ArrayCollection();
        // $this->User = new ArrayCollection();
        // $this->RapportVeterinaire = new ArrayCollection();
    }

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

    public function getNourriture(): ?string
    {
        return $this->Nourriture;
    }

    public function setNourriture(string $Nourriture): static
    {
        $this->Nourriture = $Nourriture;
        return $this;
    }

    public function getNourritureGrammageEmp(): ?int
    {
        return $this->Nourriture_grammage_emp;
    }

    public function setNourritureGrammageEmp(int $Nourriture_grammage_emp): static
    {
        $this->Nourriture_grammage_emp = $Nourriture_grammage_emp;
        return $this;
    }

    public function getAnimal(): Collection
    {
        return $this->Animal;
    }

    public function addAnimal(Animal $animal): self
    {
        if (!$this->Animal->contains($animal)) {
            $this->Animal->add($animal);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): self
    {
        $this->Animal->removeElement($animal);

        return $this;
    }

    // public function getUser(): Collection
    // {
    //     return $this->User;
    // }

    // public function addUser(User $user): static
    // {
    //     if (!$this->User->contains($user)) {
    //         $this->User->add($user);
    //     }

    //     return $this;
    // }

    // public function removeUser(User $user): static
    // {
    //     $this->User->removeElement($user);

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, RapportVeterinaire>
    //  */
    // public function getRapportVeterinaire(): Collection
    // {
    //     return $this->RapportVeterinaire;
    // }

    // public function addRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    // {
    //     if (!$this->RapportVeterinaire->contains($rapportVeterinaire)) {
    //         $this->RapportVeterinaire->add($rapportVeterinaire);
    //     }

    //     return $this;
    // }

    // public function removeRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    // {
    //     $this->RapportVeterinaire->removeElement($rapportVeterinaire);

    //     return $this;
    // }
}
