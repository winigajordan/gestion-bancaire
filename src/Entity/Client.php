<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $pays = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $pieceIdentite = null;

    #[ORM\Column(length: 255)]
    private ?string $typePieceIdentite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationPieceIdentite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $expirationPieceIdentite = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Compte::class)]
    private Collection $comptes;

    public function __construct()
    {
        $this->roles = ['ROLE_CLIENT'];
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getNaissance(): ?\DateTimeInterface
    {
        return $this->naissance;
    }

    public function setNaissance(\DateTimeInterface $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getPieceIdentite(): ?string
    {
        return $this->pieceIdentite;
    }

    public function setPieceIdentite(string $pieceIdentite): self
    {
        $this->pieceIdentite = $pieceIdentite;

        return $this;
    }

    public function getTypePieceIdentite(): ?string
    {
        return $this->typePieceIdentite;
    }

    public function setTypePieceIdentite(string $typePieceIdentite): self
    {
        $this->typePieceIdentite = $typePieceIdentite;

        return $this;
    }

    public function getCreationPieceIdentite(): ?\DateTimeInterface
    {
        return $this->creationPieceIdentite;
    }

    public function setCreationPieceIdentite(\DateTimeInterface $creationPieceIdentite): self
    {
        $this->creationPieceIdentite = $creationPieceIdentite;

        return $this;
    }

    public function getExpirationPieceIdentite(): ?\DateTimeInterface
    {
        return $this->expirationPieceIdentite;
    }

    public function setExpirationPieceIdentite(\DateTimeInterface $expirationPieceIdentite): self
    {
        $this->expirationPieceIdentite = $expirationPieceIdentite;

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
            $compte->setClient($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getClient() === $this) {
                $compte->setClient(null);
            }
        }

        return $this;
    }
}
