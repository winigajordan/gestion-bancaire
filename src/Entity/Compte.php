<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ouverture = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'source', targetEntity: Operation::class)]
    private Collection $operationSource;

    #[ORM\OneToMany(mappedBy: 'receveur', targetEntity: Operation::class)]
    private Collection $operationReception;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCompte $type = null;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: CarteBancaire::class)]
    private Collection $carteBancaires;

    public function __construct()
    {
        $this->operationSource = new ArrayCollection();
        $this->operationReception = new ArrayCollection();
        $this->carteBancaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getOuverture(): ?\DateTimeInterface
    {
        return $this->ouverture;
    }

    public function setOuverture(\DateTimeInterface $ouverture): self
    {
        $this->ouverture = $ouverture;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperationSource(): Collection
    {
        return $this->operationSource;
    }

    public function addOperationSource(Operation $operationSource): self
    {
        if (!$this->operationSource->contains($operationSource)) {
            $this->operationSource->add($operationSource);
            $operationSource->setSource($this);
        }

        return $this;
    }

    public function removeOperationSource(Operation $operationSource): self
    {
        if ($this->operationSource->removeElement($operationSource)) {
            // set the owning side to null (unless already changed)
            if ($operationSource->getSource() === $this) {
                $operationSource->setSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperationReception(): Collection
    {
        return $this->operationReception;
    }

    public function addOperationReception(Operation $operationReception): self
    {
        if (!$this->operationReception->contains($operationReception)) {
            $this->operationReception->add($operationReception);
            $operationReception->setReceveur($this);
        }

        return $this;
    }

    public function removeOperationReception(Operation $operationReception): self
    {
        if ($this->operationReception->removeElement($operationReception)) {
            // set the owning side to null (unless already changed)
            if ($operationReception->getReceveur() === $this) {
                $operationReception->setReceveur(null);
            }
        }

        return $this;
    }

    public function getType(): ?TypeCompte
    {
        return $this->type;
    }

    public function setType(?TypeCompte $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, CarteBancaire>
     */
    public function getCarteBancaires(): Collection
    {
        return $this->carteBancaires;
    }

    public function addCarteBancaire(CarteBancaire $carteBancaire): self
    {
        if (!$this->carteBancaires->contains($carteBancaire)) {
            $this->carteBancaires->add($carteBancaire);
            $carteBancaire->setCompte($this);
        }

        return $this;
    }

    public function removeCarteBancaire(CarteBancaire $carteBancaire): self
    {
        if ($this->carteBancaires->removeElement($carteBancaire)) {
            // set the owning side to null (unless already changed)
            if ($carteBancaire->getCompte() === $this) {
                $carteBancaire->setCompte(null);
            }
        }

        return $this;
    }
}
