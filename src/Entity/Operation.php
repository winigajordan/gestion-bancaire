<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OperationRepository::class)]
class Operation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'operationSource')]
    private ?Compte $source = null;

    #[ORM\ManyToOne(inversedBy: 'operationReception')]
    private ?Compte $receveur = null;

    #[ORM\ManyToOne(inversedBy: 'operations')]
    #[ORM\Column(nullable: true)]
    private ?TypeOperation $type = null;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSource(): ?Compte
    {
        return $this->source;
    }

    public function setSource(?Compte $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getReceveur(): ?Compte
    {
        return $this->receveur;
    }

    public function setReceveur(?Compte $receveur): self
    {
        $this->receveur = $receveur;

        return $this;
    }

    public function getType(): ?TypeOperation
    {
        return $this->type;
    }

    public function setType(?TypeOperation $type): self
    {
        $this->type = $type;

        return $this;
    }
}
