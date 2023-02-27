<?php

namespace App\DataFixtures;

use App\Entity\Caissier;
use App\Entity\TypeCompte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Webmozart\Assert\Tests\StaticAnalysis\length;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {
       $caissier = (new Caissier())
            ->setNom('Nom Caissier 1')
            ->setPrenom('Prenom Caissier 1')
           ->setIdentifiant(uniqid('ca-'))
               ->setEmail("caissier@lightbank.com");
       $password = $this->hasher->hashPassword($caissier, '1234');
       $caissier->setPassword($password);
       $manager->persist($caissier);


       $manager->flush();
    }
}
