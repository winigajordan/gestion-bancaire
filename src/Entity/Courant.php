<?php

namespace App\Entity;

use App\Repository\CourantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourantRepository::class)]
class Courant extends Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $tauxAgios = null;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: Carte::class)]
    private Collection $cartes;

    public function __construct()
    {
        parent::__construct();
        $this->cartes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getTauxAgios(): ?float
    {
        return $this->tauxAgios;
    }

    public function setTauxAgios(float $tauxAgios): self
    {
        $this->tauxAgios = $tauxAgios;

        return $this;
    }

    /**
     * @return Collection<int, Carte>
     */
    public function getCartes(): Collection
    {
        return $this->cartes;
    }

    public function addCarte(Carte $carte): self
    {
        if (!$this->cartes->contains($carte)) {
            $this->cartes->add($carte);
            $carte->setCompte($this);
        }

        return $this;
    }

    public function removeCarte(Carte $carte): self
    {
        if ($this->cartes->removeElement($carte)) {
            // set the owning side to null (unless already changed)
            if ($carte->getCompte() === $this) {
                $carte->setCompte(null);
            }
        }

        return $this;
    }
}
