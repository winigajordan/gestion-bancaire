<?php

namespace App\Entity;

use App\Repository\TypeCompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCompteRepository::class)]
class TypeCompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $fraisOuverture = null;

    #[ORM\Column]
    private ?float $soldeMin = null;

    #[ORM\Column]
    private ?float $plafondRetrait = null;

    #[ORM\Column]
    private ?float $tauxInteret = null;

    #[ORM\Column]
    private ?float $tauxInteretDecouvert = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Compte::class)]
    private Collection $comptes;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFraisOuverture(): ?float
    {
        return $this->fraisOuverture;
    }

    public function setFraisOuverture(float $fraisOuverture): self
    {
        $this->fraisOuverture = $fraisOuverture;

        return $this;
    }

    public function getSoldeMin(): ?float
    {
        return $this->soldeMin;
    }

    public function setSoldeMin(float $soldeMin): self
    {
        $this->soldeMin = $soldeMin;

        return $this;
    }

    public function getPlafondRetrait(): ?float
    {
        return $this->plafondRetrait;
    }

    public function setPlafondRetrait(float $plafondRetrait): self
    {
        $this->plafondRetrait = $plafondRetrait;

        return $this;
    }

    public function getTauxInteret(): ?float
    {
        return $this->tauxInteret;
    }

    public function setTauxInteret(float $tauxInteret): self
    {
        $this->tauxInteret = $tauxInteret;

        return $this;
    }

    public function getTauxInteretDecouvert(): ?float
    {
        return $this->tauxInteretDecouvert;
    }

    public function setTauxInteretDecouvert(float $tauxInteretDecouvert): self
    {
        $this->tauxInteretDecouvert = $tauxInteretDecouvert;

        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            $compte->setType($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getType() === $this) {
                $compte->setType(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
