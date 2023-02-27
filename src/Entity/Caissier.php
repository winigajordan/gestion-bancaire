<?php

namespace App\Entity;

use App\Repository\CaissierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaissierRepository::class)]
class Caissier extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct()
    {
        $this->setRoles(['ROLE_CAISSIER']) ;
    }

    public function getId(): ?int
    {
        return parent::getId();
    }
}
